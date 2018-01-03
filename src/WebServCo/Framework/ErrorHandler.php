<?php
namespace WebServCo\Framework;

final class ErrorHandler
{
    /**
     * Registers as the error handler.
     *
     * @return bool
     */
    final public static function set()
    {
        set_error_handler(['\WebServCo\Framework\ErrorHandler', 'handle']);
        return true;
    }
    
    /**
     * Restores default error handler.
     *
     * @return bool
     */
    final public static function restore()
    {
        return restore_error_handler();
    }
    
    /**
     * Handle errors.
     *
     * @param int $errno  Error level
     * @param string $errstr Error message
     * @param string $errfile Filename the error was raised in
     * @param int $errline Line number the error was raised at
     *
     * @throws ErrorException
     */
    final public static function handle($errno, $errstr, $errfile, $errline)
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
