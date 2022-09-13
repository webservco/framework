<?php

namespace WebServCo\Framework\Exceptions;

final class NotImplementedException extends HttpException
{
    const CODE = 501;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
