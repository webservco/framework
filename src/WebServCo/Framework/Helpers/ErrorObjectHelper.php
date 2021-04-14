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
}
