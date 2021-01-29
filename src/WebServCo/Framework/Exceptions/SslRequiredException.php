<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

final class SslRequiredException extends HttpException
{
    const CODE = 400;

    public function __construct(string $message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
