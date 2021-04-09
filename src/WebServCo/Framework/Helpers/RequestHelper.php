<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

final class RequestHelper
{
    public static function getRemoteAddress(): string
    {
        if (PhpHelper::isCli()) {
            return \gethostbyname(\php_uname('n'));
        }


        if (\array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return \WebServCo\Framework\Utils\Request::sanitizeString($_SERVER['REMOTE_ADDR']);
        }

        return '';
    }
}
