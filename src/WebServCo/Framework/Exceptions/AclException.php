<?php

namespace WebServCo\Framework\Exceptions;

class AclException extends HttpException
{
    const CODE = 400;

    public function __construct($message, $code = self::CODE, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
