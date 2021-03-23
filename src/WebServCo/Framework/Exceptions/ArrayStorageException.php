<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

final class ArrayStorageException extends ApplicationException
{

    public const CODE = 0;

    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
