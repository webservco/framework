<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use ErrorException;
use WebServCo\Framework\Helpers\ErrorTypeHelper;

use function error_clear_last;
use function ini_set;
use function restore_error_handler;
use function set_error_handler;
use function sprintf;

final class ErrorHandler
{
    /**
     * Restores default error handler.
     */
    public static function restore(): bool
    {
        return restore_error_handler();
    }

    /**
     * Registers as the error handler.
     */
    public static function set(): bool
    {
        ini_set('display_errors', '0');
        set_error_handler(['\WebServCo\Framework\ErrorHandler', 'throwErrorException']);

        return true;
    }

    /**
     * Throw ErrorException.
     *
     * @param int $errno Error level
     * @param string $errstr Error message
     * @param string $errfile Filename the error was raised in
     * @param int $errline Line number the error was raised at
     * @throws \ErrorException
     */
    public static function throwErrorException(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        /* Handle error reporting disabled or suppressed *
        // CURRENT SITUATION: ignore error reporting disabled or suppressed, handle all errors
        // Custom error handler is called even if errors disable or suppressed (@)
        // Code below handles this.
        // https://www.php.net/manual/en/language.operators.errorcontrol.php
        // https://www.php.net/manual/en/function.set-error-handler.php
        // https://www.php.net/manual/en/migration80.incompatible.php
        if (!(\error_reporting() & $errno)) { // bitwise operator, not a typo
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }
        /* Handle error reporting disabled or suppressed */

        // Make sure \error_get_last will not report this again (used in ErrorObjectHelper::get())
        error_clear_last();

        throw new ErrorException(
            // message
            sprintf('%s: %s.', ErrorTypeHelper::getString($errno), $errstr),
            // code
            0,
            // severity
            $errno,
            // filename
            $errfile,
            // lineno
            $errline,
            // previous
            null,
        );
    }
}
