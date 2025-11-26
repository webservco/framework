<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\Cookie;

final class CookieLibraryHelper extends AbstractLibraryHelper
{
    private static ?Cookie $object = null;

    public static function library(): Cookie
    {
        if (!self::$object instanceof Cookie) {
            $settings = ConfigLibraryHelper::getSettings('Cookie');
            self::$object = new Cookie($settings);
        }

        return self::$object;
    }
}
