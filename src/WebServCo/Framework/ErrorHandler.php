<?php

declare(strict_types=1);

namespace WebServCo\Framework;

final class ErrorHandler
{
    public static function getErrorTypeString(int $type): string
    {
        switch ($type) {
            case \E_ERROR: // 1
                return 'E_ERROR';
            case \E_WARNING: // 2
                return 'Warning';
            case \E_PARSE: // 4
                return 'E_PARSE';
            case \E_NOTICE: // 8
                return 'Notice';
            case \E_CORE_ERROR: // 16
                return 'E_CORE_ERROR';
            case \E_CORE_WARNING: // 32
                return 'E_CORE_WARNING';
            case \E_COMPILE_ERROR: // 64
                return 'E_COMPILE_ERROR';
            case \E_COMPILE_WARNING: // 128
                return 'E_COMPILE_WARNING';
            case \E_USER_ERROR: // 256
                return 'E_USER_ERROR';
            case \E_USER_WARNING: // 512
                return 'E_USER_WARNING';
            case \E_USER_NOTICE: // 1024
                return 'E_USER_NOTICE';
            case \E_STRICT: // 2048
                return 'E_STRICT';
            case \E_RECOVERABLE_ERROR: // 4096
                return 'E_RECOVERABLE_ERROR';
            case \E_DEPRECATED: // 8192
                return 'Deprecated';
            case \E_USER_DEPRECATED: // 16384
                return 'E_USER_DEPRECATED';
            case \E_ALL: // 32767
                return 'E_ALL';
            default:
                return 'Unknown';
        }
    }

    public static function getFormattedMessage(\Throwable $exception): string
    {
        return \sprintf(
            'Error: %s in %s:%s.',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
        );
    }

    /*
    * Get a \Throwable object if an error has occured.
    *
    * Used only for error logging / information display, not actually thrown.
    */
    public static function getThrowable(?\Throwable $exception = null): ?\Throwable
    {
        // Regular Exception, nothing further to do

        if ($exception instanceof \Throwable) {
            return $exception;
        }

        // A regular Error: create an ErrorException
        // There is already a sys to convert and Error to ErrorException, so in theory we should never arrive here.

        $last_error = \error_get_last();

        if ($last_error) {
            return new \ErrorException(
                $last_error['message'], // message
                0, // code
                $last_error['type'], // severity
                $last_error['file'], // filename
                $last_error['line'], // lineno
                null, // previous
            );
        }

        // No error

        return null;
    }

    /**
     * Restores default error handler.
     */
    public static function restore(): bool
    {
        return \restore_error_handler();
    }

    /**
     * Registers as the error handler.
     */
    public static function set(): bool
    {
        self::disableErrorDisplay();
        \set_error_handler(['\WebServCo\Framework\ErrorHandler', 'throwErrorException']);
        return true;
    }

    /**
     * Throws ErrorException.
     *
     * @param int $errno Error level
     * @param string $errstr Error message
     * @param string $errfile Filename the error was raised in
     * @param int $errline Line number the error was raised at
     * @throws \ErrorException
     */
    public static function throwErrorException(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        /* Handle error reporting disabled or supressed *
        // CURRENT SITUATION: ignore error reporting disabled or supressed, handle all errors
        // Custom error handler is called even if errors disable or supressed (@)
        // Code below handles this.
        // https://www.php.net/manual/en/language.operators.errorcontrol.php
        // https://www.php.net/manual/en/function.set-error-handler.php
        if (!(\error_reporting() & $errno)) { // bitwise operator, not a typo
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }
        /* Handle error reporting disabled or supressed */

        throw new \ErrorException(
            \sprintf('%s: %s.', self::getErrorTypeString($errno), $errstr), // message
            0, // code
            $errno, // severity
            $errfile, // filename
            $errline, // lineno
            null, // previous
        );
    }

    /**
     * Disable error display.
     */
    protected static function disableErrorDisplay(): bool
    {
        \ini_set('display_errors', '0');
        return true;
    }
}
