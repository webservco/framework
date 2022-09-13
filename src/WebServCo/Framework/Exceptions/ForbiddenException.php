<?php

namespace WebServCo\Framework\Exceptions;

class ForbiddenException extends AclException
{
    const CODE = 403;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
