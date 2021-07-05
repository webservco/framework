<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

class HttpStatusCodeException extends HttpException
{

    public const CODE = 500;

    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
