<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

class MethodNotAllowedException extends HttpException
{
    public const int CODE = 405;

    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
