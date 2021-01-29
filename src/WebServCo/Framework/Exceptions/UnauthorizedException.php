<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

class UnauthorizedException extends AclException
{
    const CODE = 401;

    public function __construct(string $message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
