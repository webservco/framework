<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

class ErrorMessageHelper
{
    public static function format(\Throwable $exception): string
    {
        return \sprintf(
            'Error: %s in %s:%s.',
            $exception->getMessage(),
            // Do not use full path for security reasons; errors are sometimes forwarded to customers.
            \basename($exception->getFile(), '.php'),
            $exception->getLine(),
        );
    }
}
