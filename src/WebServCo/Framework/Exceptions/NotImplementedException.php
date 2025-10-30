<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

final class NotImplementedException extends HttpException
{
    public const int CODE = 501;

    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
