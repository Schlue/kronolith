<?php
/**
 * Basic Kronolith test case.
 *
 * PHP version 5
 *
 * @category   Horde
 * @package    Kronolith
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @link       http://www.horde.org/apps/kronolith
 * @license    http://www.horde.org/licenses/gpl GNU General Public License, version 2
 */

/**
 * Basic Kronolith test case.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (ASL).  If you did
 * did not receive this file, see http://www.horde.org/licenses/apache.
 *
 * @category   Horde
 * @package    Kronolith
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @link       http://www.horde.org/apps/kronolith
 * @license    http://www.horde.org/licenses/gpl GNU General Public License, version 2
 */

// use the following namespace
use PHPUnit\Framework\TestCase;

class Kronolith_TestCase
extends TestCase
{
    protected function getInjector()
    {
        return new Horde_Injector(new Horde_Injector_TopLevel());
    }

    protected static function createSqlPdoSqlite(Horde_Test_Setup $setup)
    {
        $setup->setup(array('Horde_Db_Adapter' => array('factory' => 'Db')));
        $GLOBALS['injector']->bindImplementation('Kronolith_Geo', 'Kronolith_Geo_Sql');
    }

    protected static function createBasicKronolithSetup(Horde_Test_Setup $setup)
    {
        $setup->setup(
            array(
                '_PARAMS' => array(
                    'user' => 'test@example.com',
                    'app' => 'kronolith'
                ),
                'Horde_Alarm' => 'Alarm',
                'Horde_Cache' => 'Cache',
                'Horde_Group' => 'Group',
                'Horde_History' => 'History',
                'Horde_Prefs' => 'Prefs',
                'Horde_Perms' => 'Perms',
                'Horde_Registry' => 'Registry',
                'Horde_Session' => 'Session',
            )
        );
        $setup->makeGlobal(
            array(
                'injector' => 'Horde_Injector',
                'prefs' => 'Horde_Prefs',
                'registry' => 'Horde_Registry',
                'session' => 'Horde_Session',
            )
        );
        $GLOBALS['injector']->setInstance('Content_Objects_Manager', new Content_Objects_Manager());
        $GLOBALS['injector']->setInstance('Content_Tagger', new Content_Tagger());
        $GLOBALS['injector']->setInstance('Content_Types_Manager', new Content_Types_Manager());
        $GLOBALS['conf']['prefs']['driver'] = 'Null';
        $GLOBALS['conf']['sql']['charset'] = 'utf-8';
        $GLOBALS['conf']['sql']['driverconfig'] = 'Horde';
        $GLOBALS['calendar_manager'] = new Kronolith_Stub_CalendarManager();
    }

    protected static function createSqlShares(Horde_Test_Setup $setup)
    {
        $setup->getInjector()->setInstance(
            'Horde_Core_Factory_Db',
            new Horde_Test_Stub_Factory(
                $setup->getInjector()->getInstance('Horde_Db_Adapter')
            )
        );
        $setup->setup(
            array(
                'Kronolith_Shares' => 'Share',
            )
        );
        $GLOBALS['injector']->setInstance(
            'Horde_Core_Factory_Share',
            new Kronolith_Stub_ShareFactory($setup->getInjector()->getInstance('Kronolith_Shares'))
        );
        $GLOBALS['conf']['storage']['driver'] = 'sql';
        $GLOBALS['conf']['calendar']['driver'] = 'default';
    }

    protected static function createKolabShares(Horde_Test_Setup $setup)
    {
        $setup->setup(
            array(
                'Horde_Kolab_Storage' => array(
                    'factory' => 'KolabStorage',
                    'params' => array(
                        'imapuser' => 'test',
                    )
                ),
                'Kronolith_Shares' => array(
                    'factory' => 'Share',
                    'method' => 'Kolab',
                ),
            )
        );
        $GLOBALS['injector']->setInstance(
            'Horde_Core_Factory_Share',
            new Kronolith_Stub_ShareFactory($setup->getInjector()->getInstance('Kronolith_Shares'))
        );
        $GLOBALS['conf']['storage']['driver'] = 'kolab';
        $GLOBALS['conf']['calendar']['driver'] = 'kolab';
    }

    protected static function createKolabSetup()
    {
        $setup = new Horde_Test_Setup();
        self::createBasicKronolithSetup($setup);
        self::createKolabShares($setup);
        self::_createDefaultShares();

        return $setup;
    }

    protected static function _createDefaultShares()
    {
        $share = self::_createShare(
            'Calendar of Tester', 'test@example.com'
        );
        $other_share = self::_createShare(
            'Other calendar of Tester', 'test@example.com'
        );
        return array($share, $other_share);
    }

    private static function _createShare($name, $owner)
    {
        $share = $GLOBALS['injector']->getInstance('Kronolith_Shares')->newShare(
            $owner, strval(new Horde_Support_Randomid()), $name
        );
        $GLOBALS['injector']->getInstance('Kronolith_Shares')->addShare($share);
        $GLOBALS['all_calendars'][$share->getName()] = new Kronolith_Calendar_Internal(array('share' => $share));
        return $share;
    }
}
