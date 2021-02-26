<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\Config;

abstract class ConfigLibraryHelper extends AbstractLibraryHelper
{
    private static ?Config $object = null;

    /**
    * @return array<mixed>
    */
    public static function getSettings(string $name): array
    {
        $projectPath = self::library()->get(
            \sprintf(
                'app%1$spath%1$sproject',
                \WebServCo\Framework\Settings::DIVIDER,
            ),
        );
        if (empty($projectPath)) {
            return [];
        }
        return self::library()->load($name, $projectPath);
    }

    public static function library(): Config
    {
        if (!self::$object instanceof Config) {
            self::$object = new Config();
        }
        return self::$object;
    }
}
