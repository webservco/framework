<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

use Throwable;

// @phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal.ClassNeitherAbstractNorFinal
class NotFoundException extends HttpException
{
    public const int CODE = 404;

    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
