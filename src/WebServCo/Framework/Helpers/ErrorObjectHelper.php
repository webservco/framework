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
        // and also clears the last error.
        // $lastError will contain data if an error happens before PHP script exection
        // (so before the error handler is registered)
        $lastError = \error_get_last();

        if ($lastError) {
            \error_clear_last();
            $errorException = new \ErrorException(
                $lastError['message'], // message
                0, // code
                $lastError['type'], // severity
                $lastError['file'], // filename
                $lastError['line'], // lineno
                null, // previous
            );

            // Handle: "Error: POST Content-Length of X bytes exceeds the limit of Y bytes in Unknown:0."
            if (false !== \strpos($lastError['message'], 'POST Content-Length of ')) {
                return new \WebServCo\Framework\Exceptions\UploadException(
                    \WebServCo\Framework\Files\Upload\Codes::INI_SIZE, // code
                    $errorException, // previous
                );
            }

            return $errorException;
        }

        // No error

        return null;
    }
}
