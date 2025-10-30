<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

class UnauthorizedException extends AclException
{
    public const int CODE = 401;

    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
