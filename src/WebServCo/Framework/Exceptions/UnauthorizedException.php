<?php
namespace WebServCo\Framework\Exceptions;

class UnauthorizedException extends AclException
{
    const CODE = 401;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
