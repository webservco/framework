<?php declare(strict_types = 1);

namespace WebServCo\Framework\Exceptions;

class UnsupportedMediaTypeException extends HttpException
{
    const CODE = 415;

    public function __construct(string $message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
