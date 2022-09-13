<?php

namespace WebServCo\Framework\Exceptions;

class HttpBrowserException extends HttpException
{
    public const CODE = 500;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
