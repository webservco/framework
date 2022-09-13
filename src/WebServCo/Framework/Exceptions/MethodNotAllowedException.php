<?php

namespace WebServCo\Framework\Exceptions;

class MethodNotAllowedException extends HttpException
{
    const CODE = 405;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
