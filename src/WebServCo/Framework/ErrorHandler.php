<?php
namespace WebServCo\Framework;

final class ErrorHandler
{
    /**
     * Registers as the error handler.
     *
     * @return bool
     */
    public static function set()
    {
        self::disableErrorDisplay();
        set_error_handler(['\WebServCo\Framework\ErrorHandler', 'throwErrorException']);
        return true;
    }

    /**
     * Disable error display.
     */
    protected static function disableErrorDisplay()
    {
        ini_set('display_errors', 0);
    }

    /**
     * Restores default error handler.
     *
     * @return bool
     */
    public static function restore()
    {
        return restore_error_handler();
    }

    /**
     * Throws ErrorException.
     *
     * @param int $errno Error level
     * @param string $errstr Error message
     * @param string $errfile Filename the error was raised in
     * @param int $errline Line number the error was raised at
     *
     * @throws \ErrorException
     */
    public static function throwErrorException($errno, $errstr, $errfile, $errline)
    {
        if (error_reporting() & $errno) { //check if error level is set
            throw new \ErrorException(
                sprintf('%s: %s', self::getErrorTypeString($errno), $errstr),
                0,
                $errno,
                $errfile,
                $errline
            );
        }
    }

    public static function getErrorTypeString($type)
    {
        switch ($type) {
            case E_ERROR: // 1
                return 'E_ERROR';
            case E_WARNING: // 2
                return 'Warning';
            case E_PARSE: // 4
                return 'E_PARSE';
            case E_NOTICE: // 8
                return 'Notice';
            case E_CORE_ERROR: // 16
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192
                return 'Deprecated';
            case E_USER_DEPRECATED: // 16384
                return 'E_USER_DEPRECATED';
            case E_ALL: // 32767
                return 'E_ALL';
            default:
                return 'Unknown';
        }
    }
}
