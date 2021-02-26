<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\MysqlPdoDatabase;

abstract class MysqlPdoDatabaseLibraryHelper extends AbstractLibraryHelper
{
    private static ?MysqlPdoDatabase $object = null;

    public static function library(): MysqlPdoDatabase
    {
        if (!self::$object instanceof MysqlPdoDatabase) {
            $settings = ConfigLibraryHelper::getSettings('MysqlPdoDatabase');
            self::$object = new MysqlPdoDatabase($settings);
        }
        return self::$object;
    }
}
