<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

final class SslRequiredException extends HttpException
{
    public const int CODE = 400;

    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
