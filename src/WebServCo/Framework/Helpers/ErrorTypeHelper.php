<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use const E_ALL;
use const E_COMPILE_ERROR;
use const E_COMPILE_WARNING;
use const E_CORE_ERROR;
use const E_CORE_WARNING;
use const E_DEPRECATED;
use const E_ERROR;
use const E_NOTICE;
use const E_PARSE;
use const E_RECOVERABLE_ERROR;
use const E_USER_DEPRECATED;
use const E_USER_ERROR;
use const E_USER_NOTICE;
use const E_USER_WARNING;
use const E_WARNING;

final class ErrorTypeHelper
{
    public static function getString(int $type): string
    {
        switch ($type) {
            // 1
            case E_ERROR:
                return 'E_ERROR';
            // 2
            case E_WARNING:
                return 'Warning';
            // 4
            case E_PARSE:
                return 'E_PARSE';
            // 8
            case E_NOTICE:
                return 'Notice';
            // 16
            case E_CORE_ERROR:
                return 'E_CORE_ERROR';
            // 32
            case E_CORE_WARNING:
                return 'E_CORE_WARNING';
            // 64
            case E_COMPILE_ERROR:
                return 'E_COMPILE_ERROR';
            // 128
            case E_COMPILE_WARNING:
                return 'E_COMPILE_WARNING';
            // 256
            case E_USER_ERROR:
                return 'E_USER_ERROR';
            // 512
            case E_USER_WARNING:
                return 'E_USER_WARNING';
            // 1024
            case E_USER_NOTICE:
                return 'E_USER_NOTICE';

            /* This error level is unused, and has been deprecated as of PHP 8.4.0.
             * case \E_STRICT: // 2048
                return 'E_STRICT';*/
            // 4096
            case E_RECOVERABLE_ERROR:
                return 'E_RECOVERABLE_ERROR';
            // 8192
            case E_DEPRECATED:
                return 'Deprecated';
            // 16384
            case E_USER_DEPRECATED:
                return 'E_USER_DEPRECATED';
            // 32767
            case E_ALL:
                return 'E_ALL';
            default:
                return 'Unknown';
        }
    }
}
