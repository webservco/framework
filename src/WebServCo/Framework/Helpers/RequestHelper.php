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

        // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
        if (\array_key_exists('REMOTE_ADDR', $_SERVER)) {
            // phpcs:ignore SlevomatCodingStandard.Variables.DisallowSuperGlobalVariable.DisallowedSuperGlobalVariable
            return \WebServCo\Framework\Utils\Request::sanitizeString($_SERVER['REMOTE_ADDR']);
        }

        return '';
    }
}
