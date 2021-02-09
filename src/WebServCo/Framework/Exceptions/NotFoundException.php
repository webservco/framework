<?php declare(strict_types = 1);

namespace WebServCo\Framework\Exceptions;

final class NotFoundException extends HttpException
{

    public const CODE = 404;

    public function __construct(string $message, ?\Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
