<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

class ErrorObjectHelper
{
    /*
    * Get a \Throwable object if an error has occured.
    *
    * Used only for error logging / information display, not actually thrown.
    */
    public static function get(?\Throwable $exception = null): ?\Throwable
    {
        // Regular Exception, nothing further to do

        if ($exception instanceof \Throwable) {
            return $exception;
        }

        // A regular Error: create an ErrorException
        // ErrorHandler.throwErrorException already converts Error to ErrorException,
        // and also clears the last error
        // so in theory $lastError should always be empty.

        $lastError = \error_get_last();

        if ($lastError) {
            return new \ErrorException(
                $lastError['message'], // message
                0, // code
                $lastError['type'], // severity
                $lastError['file'], // filename
                $lastError['line'], // lineno
                null, // previous
            );
        }

        // No error

        return null;
    }
}
