<?php
/**
 * Test the core Kronolith driver with a sqlite DB.
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
 * Test the core Kronolith driver with a sqlite DB.
 *
 * Copyright 2011-2017 Horde LLC (http://www.horde.org/)
 *
 * See the enclosed file LICENSE for license information (GPLv2). If you did not
 * receive this file, see http://www.horde.org/licenses/gpl
 *
 * @category   Horde
 * @package    Kronolith
 * @subpackage UnitTests
 * @author     Gunnar Wrobel <wrobel@pardus.de>
 * @link       http://www.horde.org/apps/kronolith
 * @license    http://www.horde.org/licenses/gpl GNU General Public License, version 2
 */
class Kronolith_Integration_Kronolith_Sql_Pdo_SqliteTest extends Kronolith_Integration_Kronolith_Sql_Base
{
    protected $backupGlobals = false;

    public static function setUpBeforeClass(): void
    {
        self::$callback = array(__CLASS__, 'getDb');
        parent::setUpBeforeClass();
        $migrator = new Horde_Db_Migration_Migrator(
            $GLOBALS['injector']->getInstance('Horde_Db_Adapter'),
            null,
            array(
                'migrationsPath' => __DIR__ . '/../../../../../../migration',
                'schemaTableName' => 'kronolith_test_schema'
            )
        );
        $migrator->up();
    }

    protected static function getDb()
    {
        self::createSqlPdoSqlite(self::$setup);
    }
}
