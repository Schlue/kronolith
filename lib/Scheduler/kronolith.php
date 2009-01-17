<?php
/**
 * @package Horde_Scheduler
 */

/** Date_Calc */
require_once 'Date/Calc.php';

/** Horde_Date */
require_once 'Horde/Date.php';

/** Horde_Date_Recurrence */
require_once 'Horde/Date/Recurrence.php';

/** Horde_Scheduler */
require_once 'Horde/Scheduler.php';

/** Horde_Share */
require_once 'Horde/Share.php';

/** Kronolith */
require_once KRONOLITH_BASE . '/lib/Kronolith.php';

/** Kronolith_Driver */
require_once KRONOLITH_BASE . '/lib/Driver.php';

/**
 * Horde_Scheduler_kronolith::
 *
 * Act on alarms in events and send emails/pages/etc. to users.
 *
 * $Horde: kronolith/lib/Scheduler/kronolith.php,v 1.74 2008/10/20 16:54:07 jan Exp $
 *
 * @package Horde_Scheduler
 */
class Horde_Scheduler_kronolith extends Horde_Scheduler {

    /**
     * The list of calendars. We store this so we're not fetching it all the
     * time, but update the cache occasionally to find new calendars.
     *
     * @var array
     */
    var $_calendars = array();

    /**
     * The last timestamp that we ran.
     *
     * @var integer
     */
    var $_runtime;

    /**
     * The last time we fetched the full calendar list.
     *
     * @var integer
     */
    var $_listtime;

    /**
     * The last time we processed agendas.
     *
     * @var integer
     */
    var $_agendatime;

    /**
     */
    function Horde_Scheduler_kronolith($params = array())
    {
        parent::Horde_Scheduler($params);

        // Load the Registry and setup conf, etc.
        $GLOBALS['registry'] = &Registry::singleton(HORDE_SESSION_NONE);
        $GLOBALS['registry']->pushApp('kronolith', false);

        // Notification instance for code that relies on it.
        $GLOBALS['notification'] = &Notification::singleton();

        // Create a share instance. This must exist in the global scope for
        // Kronolith's API calls to function properly.
        $GLOBALS['shares'] = &Horde_Share::singleton($GLOBALS['registry']->getApp());

        // Create a calendar backend object. This must exist in the global
        // scope for Kronolith's API calls to function properly.
        $GLOBALS['kronolith_driver'] = &Kronolith_Driver::factory();
    }

    /**
     */
    function run()
    {
        if (isset($_SERVER['REQUEST_TIME'])) {
            $this->_runtime = $_SERVER['REQUEST_TIME'];
        } else {
            $this->_runtime = time();
        }

        // If we haven't fetched the list of calendars in over an hour,
        // re-list to pick up any new ones.
        if ($this->_runtime - $this->_listtime > 3600) {
            $this->_listtime = $this->_runtime;
            $this->_calendars = $GLOBALS['shares']->listAllShares();
        }

        // If there are no calendars to check, we're done.
        if (!count($this->_calendars)) {
            return;
        }

        if (!empty($GLOBALS['conf']['reminder']['server_name'])) {
            $GLOBALS['conf']['server']['name'] = $GLOBALS['conf']['reminder']['server_name'];
        }

        // Send agendas every hour.
        if ($this->_runtime - $this->_agendatime >= 0) {
            $this->agenda();
        }
    }

    /**
     */
    function agenda()
    {
        // Send agenda only once per day.
        if (date('z', $this->_runtime) == date('z', $this->_agendatime)) {
            //return;
        }

        // Retrieve a list of users associated with each calendar, and
        // thus a list of users who have used kronolith and
        // potentially have an agenda preference set.
        $users = array();
        foreach (array_keys($this->_calendars) as $calendarId) {
            $calendar = $GLOBALS['shares']->getShare($calendarId);
            if (is_a($calendar, 'PEAR_Error')) {
                continue;
            }
            $users = array_merge($users, $calendar->listUsers(PERMS_READ));
        }

        // Remove duplicates.
        $users = array_unique($users);

        $runtime = new Horde_Date($this->_runtime);
        $default_timezone = date_default_timezone_get();

        // Loop through the users and generate an agenda for them
        foreach ($users as $user) {
            $prefs = &Prefs::singleton($GLOBALS['conf']['prefs']['driver'],
                                       'kronolith', $user);
            $prefs->retrieve();
            $agenda_calendars = $prefs->getValue('daily_agenda');

            // Check if user has a timezone pref, and set it. Otherwise, make
            // sure to use the server's default timezone.
            $tz = $prefs->getValue('timezone');
            date_default_timezone_set(empty($tz) ? $default_timezone : $tz);

            if (!$agenda_calendars) {
                continue;
            }

            require_once 'Horde/Identity.php';

            // try to find an email address for the user
            $identity = &Identity::singleton('none', $user);
            $email = $identity->getValue('from_addr');
            if (strstr($email, '@')) {
                list($mailbox, $host) = explode('@', $email);
                $email = Horde_Mime_Address::writeAddress($mailbox, $host,
                                                  $identity->getValue('fullname'));
            }

            if (empty($email)) {
                continue;
            }

            // If we found an email address, generate the agenda.
            switch ($agenda_calendars) {
            case 'owner':
                $calendars = $GLOBALS['shares']->listShares($user, PERMS_SHOW,
                                                            $user);
                break;
            case 'read':
                $calendars = $GLOBALS['shares']->listShares($user, PERMS_SHOW,
                                                            null);
                break;
            case 'show':
            default:
                $calendars = array();
                $shown_calendars = unserialize($prefs->getValue('display_cals'));
                $cals = $GLOBALS['shares']->listShares(
                    $user, PERMS_SHOW, null);
                foreach ($cals as $calId => $cal) {
                    if (in_array($calId, $shown_calendars)) {
                        $calendars[$calId] = $cal;
                    }
                }
            }

            // Get a list of events for today
            $eventlist = array();
            foreach ($calendars as $calId => $calendar) {
                $GLOBALS['kronolith_driver']->open($calId);
                $events = $GLOBALS['kronolith_driver']->listEvents($runtime,
                                                                   $runtime);
                foreach ($events as $eventId) {
                    $event = $GLOBALS['kronolith_driver']->getEvent($eventId);
                    if (is_a($event, 'PEAR_Error')) {
                        return $event;
                    }
                    // The event list contains events starting at 12am.
                    if ($event->start->mday != $runtime->mday) {
                        continue;
                    }
                    $eventlist[$event->start->strftime('%Y%m%d%H%M%S')] = $event;
                }
            }

            if (!count($eventlist)) {
                continue;
            }

            // If there are any events, generate and send the email.
            ksort($eventlist);
            $lang = $prefs->getValue('language');
            $twentyFour = $prefs->getValue('twentyFour');
            $dateFormat = $prefs->getValue('date_format');
            NLS::setLang($lang);
            NLS::setTextdomain('kronolith', KRONOLITH_BASE . '/locale',
            NLS::getCharset());
            String::setDefaultCharset(NLS::getCharset());
            $mime_mail = new Horde_Mime_Mail(sprintf(_("Your daily agenda for %s"), strftime($dateFormat, $this->_runtime)),
                                            null,
                                            'CalendarReminders:;',
                                            $GLOBALS['conf']['reminder']['from_addr'],
                                            NLS::getCharset());

            $mail_driver = $GLOBALS['conf']['mailer']['type'];
            $mail_params = $GLOBALS['conf']['mailer']['params'];
            if ($mail_driver == 'smtp' && $mail_params['auth'] &&
                empty($mail_params['username'])) {
                Horde::logMessage('Agenda Notifications don\'t work with user based SMTP authentication.',
                                  __FILE__, __LINE__, PEAR_LOG_ERR);
                return;
            }
            $pad = max(String::length(_("All day")) + 2, $twentyFour ? 6 : 8);

            $message = sprintf(_("Your daily agenda for %s"),
                               strftime($dateFormat, $this->_runtime))
                . "\n\n";
            foreach ($eventlist as $event) {
                if ($event->isAllDay()) {
                    $message .= str_pad(_("All day") . ':', $pad);
                } else {
                    $message .= str_pad($event->start->format($twentyFour  ? 'H:i' : 'h:ia'),
                                        $pad);
                    }
                    $message .= $event->title . "\n";
            }

            $mime_mail->setBody($message, NLS::getCharset(), true);
            $mime_mail->addRecipients($email);
            Horde::logMessage(sprintf('Sending daily agenda to %s', $email),
                              __FILE__, __LINE__, PEAR_LOG_DEBUG);
            $sent = $mime_mail->send($mail_driver, $mail_params, false, false);
            if (is_a($sent, 'PEAR_Error')) {
                return $sent;
            }
        }

        $this->_agendatime = $this->_runtime;
    }

}
