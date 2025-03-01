<?php
/**
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (GPLv2). If you
 * did not receive this file, see http://www.horde.org/licenses/gpl
 *
 * @category   Horde
 * @package    Kronolith
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @link       http://www.horde.org/apps/kronolith
 * @license    http://www.horde.org/licenses/gpl GPLv2
 */

/**
 * Test exporting iCalendar events.
 *
 * @category   Horde
 * @package    Kronolith
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @link       http://www.horde.org/apps/kronolith
 * @license    http://www.horde.org/licenses/gpl GPLv2
 */
class Kronolith_Integration_ToIcalendarTest extends Kronolith_TestCase
{
    public function setUp(): void
    {
        $GLOBALS['registry'] = new Kronolith_Stub_Registry('test', 'kronolith');
        $GLOBALS['injector'] = new Horde_Injector(new Horde_Injector_TopLevel());
        $GLOBALS['conf']['prefs']['driver'] = 'Null';
        $GLOBALS['injector']->bindFactory('Kronolith_Geo', 'Kronolith_Factory_Geo', 'create');
        $GLOBALS['injector']->bindFactory('Horde_Alarm', 'Horde_Test_Factory_Alarm', 'create');
        $logger = new Horde_Log_Logger(new Horde_Log_Handler_Null());
        $GLOBALS['injector']->setInstance('Horde_Log_Logger', $logger);
        $GLOBALS['conf']['calendar']['driver'] = 'Mock';
    }

    public function tearDown(): void
    {
        unset($GLOBALS['registry']);
        unset($GLOBALS['injector']);
        unset($GLOBALS['conf']);
    }

    public function testBasicVersion1()
    {
        $this->_testExport($this->_getEvent(), '1.0', 'export1.ics');
    }

    public function testBasicVersion2()
    {
        $this->_testExport($this->_getEvent(), '2.0', 'export2.ics');
    }

    public function testPrivateVersion1()
    {
        $this->_testExport($this->_getPrivateEvent(), '1.0', 'export3.ics');
    }

    public function testPrivateVersion2()
    {
        $this->_testExport($this->_getPrivateEvent(), '2.0', 'export4.ics');
    }

    private function _testExport($event, $export_version, $fixture)
    {
        $ical = new Horde_Icalendar($export_version);
        $cal = $event->toiCalendar($ical);
        $ical->addComponent($cal);
        $this->assertEquals(
            $this->_removeDateStamp($this->_getFixture($fixture)),
            $this->_removeDateStamp($ical->exportvCalendar())
        );
    }

    private function _removeDateStamp($ics)
    {
        return preg_replace(
            '/DTSTAMP:\d\d\d\d\d\d\d\dT\d\d\d\d\d\dZ/',
            'DTSTAMP:--------T------Z',
            $ics
        );
    }

    private function _getEvent()
    {
        $GLOBALS['registry']->admin = true;
        $event = new Kronolith_Event_Mock(new Kronolith_Stub_Driver(''));
        $event->start = new Horde_Date('2007-03-15 13:10:20');
        $event->end = new Horde_Date('2007-03-15 14:20:00');
        $event->uid = '20070315143732.4wlenqz3edq8@horde.org';
        $event->title = 'Hübscher Termin';
        $event->description = "Schöne Bescherung\nNew line";
        $event->location = 'Allgäu';
        $event->alarm = 10;
        $event->tags = array('Schöngeistiges');
        $event->recurrence = new Horde_Date_Recurrence($event->start);
        $event->recurrence->setRecurType(Horde_Date_Recurrence::RECUR_DAILY);
        $event->recurrence->setRecurInterval(2);
        $event->recurrence->addException(2007, 3, 19);
        $event->initialized = true;
        return $event;
    }

    private function _getPrivateEvent()
    {
        $event = $this->_getEvent();
        $GLOBALS['registry']->admin = false;
        $event->creator = 'joe';
        $event->private = true;
        $event->status = Kronolith::STATUS_TENTATIVE;
        $event->recurrence = new Horde_Date_Recurrence($event->start);
        $event->recurrence->setRecurType(Horde_Date_Recurrence::RECUR_MONTHLY_DATE);
        $event->recurrence->setRecurInterval(1);
        $event->recurrence->addException(2007, 4, 15);
        $event->recurrence->addException(2007, 6, 15);
        $event->attendees = new Kronolith_Attendee_List(
            include __DIR__ . '/../fixtures/attendees.php'
        );
        return $event;
    }

    private function _getFixture($name)
    {
        return file_get_contents(__DIR__ . '/../fixtures/' . $name);
    }
}
