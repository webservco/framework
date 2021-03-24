<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\JsonOutput;

final class JsonOutputLibraryHelper extends AbstractLibraryHelper
{
    private static ?JsonOutput $object = null;

    public static function library(): JsonOutput
    {
        if (!self::$object instanceof JsonOutput) {
            $settings = ConfigLibraryHelper::getSettings('JsonOutput');
            self::$object = new JsonOutput($settings);
        }
        return self::$object;
    }
}
