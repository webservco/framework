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
        self::disableErrorDisplay();
        set_error_handler(['\WebServCo\Framework\ErrorHandler', 'throwErrorException']);
        return true;
    }
    
    /**
     * Disable error display.
     */
    final private static function disableErrorDisplay()
    {
        $logPath = Framework::OS_WINDOWS === Framework::getOS() ? 'null' : '/dev/null';
        
        if (empty(ini_get('error_log'))) {
            ini_set('error_log', $logPath);
        }
        ini_set('display_errors', 0);
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
     * Throws ErrorException.
     *
     * @param int $errno  Error level
     * @param string $errstr Error message
     * @param string $errfile Filename the error was raised in
     * @param int $errline Line number the error was raised at
     *
     * @throws ErrorException
     */
    final public static function throwErrorException($errno, $errstr, $errfile, $errline)
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
