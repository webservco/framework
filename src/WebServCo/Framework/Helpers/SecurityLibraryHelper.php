<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\Security;

final class SecurityLibraryHelper extends AbstractLibraryHelper
{
    private static ?Security $object = null;

    public static function library(): Security
    {
        if (!self::$object instanceof Security) {
            $settings = ConfigLibraryHelper::getSettings('Security');
            self::$object = new Security($settings);
        }
        return self::$object;
    }
}
