<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

final class ArrayStorageException extends ApplicationException
{
    const CODE = 0;

    public function __construct(string $message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
