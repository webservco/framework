<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

use Throwable;

// @phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal.ClassNeitherAbstractNorFinal
class HttpException extends ApplicationException
{
    public const int CODE = 400;

    public function __construct(string $message, int $code = self::CODE, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
