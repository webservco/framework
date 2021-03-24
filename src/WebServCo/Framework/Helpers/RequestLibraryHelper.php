<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\Request;

final class RequestLibraryHelper extends AbstractLibraryHelper
{
    private static ?Request $object = null;

    public static function library(): Request
    {
        if (!self::$object instanceof Request) {
            $settings = ConfigLibraryHelper::getSettings('Request');
            // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
            self::$object = new Request($settings, $_SERVER, $_POST);
        }
        return self::$object;
    }
}
