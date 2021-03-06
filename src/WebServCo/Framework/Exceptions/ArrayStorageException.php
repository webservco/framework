<?php
namespace WebServCo\Framework\Exceptions;

final class ArrayStorageException extends ApplicationException
{
    const CODE = 0;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
