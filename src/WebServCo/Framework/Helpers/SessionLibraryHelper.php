<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\Session;

abstract class SessionLibraryHelper extends AbstractLibraryHelper
{
    private static ?Session $object = null;

    public static function library(): Session
    {
        if (!self::$object instanceof Session) {
            $settings = ConfigLibraryHelper::getSettings('Session');
            self::$object = new Session($settings);
        }
        return self::$object;
    }
}
