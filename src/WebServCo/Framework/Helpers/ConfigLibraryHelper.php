<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\Config;

final class ConfigLibraryHelper extends AbstractLibraryHelper
{
    private static ?Config $object = null;

    /**
    * @return array<mixed>
    */
    public static function getSettings(string $name): array
    {
        return self::library()->load($name, \WebServCo\Framework\Environment\Config::string('APP_PATH_PROJECT'));
    }

    public static function library(): Config
    {
        if (!self::$object instanceof Config) {
            self::$object = new Config();
        }
        return self::$object;
    }
}
