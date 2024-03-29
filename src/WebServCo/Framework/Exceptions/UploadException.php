<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

class UploadException extends ApplicationException
{
    public const CODE = -1;

    public function __construct(int $code = self::CODE, ?\Throwable $previous = null)
    {
        $message = \WebServCo\Framework\Files\Upload\Codes::getMessage($code);

        parent::__construct($message, $code, $previous);
    }
}
