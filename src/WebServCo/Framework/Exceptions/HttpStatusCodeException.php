<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

use Throwable;

// @phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal.ClassNeitherAbstractNorFinal
class HttpStatusCodeException extends HttpException
{
    public const int CODE = 500;

    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
