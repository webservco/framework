<?php declare(strict_types = 1);

namespace WebServCo\Framework;

final class ErrorHandler
{
    /**
    * @param \Throwable $exception
    * @return array<string,mixed>
    */
    public static function getErrorInfo(\Throwable $exception = null): array
    {
        $errorInfo = [
            'code' => 0,
            'message' => null,
            'file' => null,
            'line' => null,
            'trace' => null,
            'exception' => null,
        ];
        if ($exception instanceof \Throwable) {
            $errorInfo['code'] = $exception->getCode();
            $errorInfo['message'] = $exception->getMessage();
            $errorInfo['file'] = $exception->getFile();
            $errorInfo['line'] = $exception->getLine();
            $errorInfo['trace'] = $exception->getTrace();
            $errorInfo['exception'] = $exception;
        } else {
            $last_error = error_get_last();
            if (!empty($last_error['message'])) {
                $errorInfo['message'] = $last_error['message'];
            }
            if (!empty($last_error['file'])) {
                $errorInfo['file'] = $last_error['file'];
            }
            if (!empty($last_error['line'])) {
                $errorInfo['line'] = $last_error['line'];
            }
        }
        return $errorInfo;
    }

    public static function getErrorTypeString(int $type): string
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
     * Registers as the error handler.
     *
     * @return bool
     */
    public static function set(): bool
    {
        self::disableErrorDisplay();
        set_error_handler(['\WebServCo\Framework\ErrorHandler', 'throwErrorException']);
        return true;
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
     * @return bool
     */
    public static function throwErrorException(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        // https://www.php.net/manual/en/function.set-error-handler.php
        if (!(error_reporting() & $errno)) { // bitwise operator, not a typo
            // This error code is not included in error_reporting, so let it fall
            // through to the standard PHP error handler
            return false;
        }

        throw new \ErrorException(
            sprintf('%s: %s.', self::getErrorTypeString($errno), $errstr),
            0,
            $errno,
            $errfile,
            $errline
        );
    }

    /**
     * Disable error display.
     */
    protected static function disableErrorDisplay(): bool
    {
        ini_set('display_errors', '0');
        return true;
    }
}
