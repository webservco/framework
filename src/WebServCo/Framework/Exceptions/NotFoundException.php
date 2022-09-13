<?php

namespace WebServCo\Framework\Exceptions;

final class NotFoundException extends HttpException
{
    const CODE = 404;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
