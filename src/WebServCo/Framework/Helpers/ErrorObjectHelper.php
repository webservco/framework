<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use ErrorException;
use Throwable;
use WebServCo\Framework\Exceptions\UploadException;
use WebServCo\Framework\Files\Upload\Codes;

use function error_clear_last;
use function error_get_last;
use function strpos;

final class ErrorObjectHelper
{
    /*
    * Get a \Throwable object if an error has occured.
    *
    * Used only for error logging / information display, not actually thrown.
    */
    public static function get(?Throwable $exception = null): ?Throwable
    {
        // Regular Exception, nothing further to do

        if ($exception instanceof Throwable) {
            return $exception;
        }

        // A regular Error: create an ErrorException
        // ErrorHandler.throwErrorException already converts Error to ErrorException,
        // and also clears the last error.
        // $lastError will contain data if an error happens before PHP script exection
        // (so before the error handler is registered)
        $lastError = error_get_last();

        if ($lastError) {
            error_clear_last();
            $errorException = new ErrorException(
                // message
                $lastError['message'],
                // code
                0,
                // severity
                $lastError['type'],
                // filename
                $lastError['file'],
                // lineno
                $lastError['line'],
                // previous
                null,
            );

            // Handle: "Error: POST Content-Length of X bytes exceeds the limit of Y bytes in Unknown:0."
            if (strpos($lastError['message'], 'POST Content-Length of ') !== false) {
                return new UploadException(
                    // code
                    Codes::INI_SIZE,
                    // previous
                    $errorException,
                );
            }

            return $errorException;
        }

        // No error

        return null;
    }
}
