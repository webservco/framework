<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

use Throwable;
use WebServCo\Framework\Files\Upload\Codes;

// @phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal.ClassNeitherAbstractNorFinal
class UploadException extends ApplicationException
{
    public const int CODE = -1;

    public function __construct(int $code = self::CODE, ?Throwable $previous = null)
    {
        $message = Codes::getMessage($code);

        parent::__construct($message, $code, $previous);
    }
}
