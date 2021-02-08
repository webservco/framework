<?php declare(strict_types = 1);

namespace WebServCo\Framework\Exceptions;

class HttpClientException extends HttpException
{
    const CODE = 500;

    public function __construct(string $message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
