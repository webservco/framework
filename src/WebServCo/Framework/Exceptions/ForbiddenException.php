<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

use Throwable;

// @phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal.ClassNeitherAbstractNorFinal
class ForbiddenException extends AclException
{
    public const int CODE = 403;

    public function __construct(string $message, ?Throwable $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
