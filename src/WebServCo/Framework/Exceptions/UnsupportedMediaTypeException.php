<?php

namespace WebServCo\Framework\Exceptions;

class UnsupportedMediaTypeException extends HttpException
{
    const CODE = 415;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
