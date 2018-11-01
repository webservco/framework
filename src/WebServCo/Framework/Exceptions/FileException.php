<?php
namespace WebServCo\Framework\Exceptions;

final class FileException extends ApplicationException
{
    const CODE = 0;

    public function __construct($message, $code = self::CODE, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
