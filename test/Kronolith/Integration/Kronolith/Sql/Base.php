<?php
/**
 * Test the core Kronolith class with a SQL backend.
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
 * Test the core Kronolith class with a SQL backend.
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
class Kronolith_Integration_Kronolith_Sql_Base extends Kronolith_Integration_Kronolith_Base
{
    static $callback;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        self::getDb();
        self::createSqlShares(self::$setup);
    }

    protected static function getDb()
    {
        call_user_func_array(self::$callback, array());
    }
}
