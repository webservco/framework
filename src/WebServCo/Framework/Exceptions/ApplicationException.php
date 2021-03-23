<?php

declare(strict_types=1);

namespace WebServCo\Framework\Exceptions;

class ApplicationException extends \Exception
{

    public const CODE = 500;

    public function __construct(string $message, int $code = self::CODE, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return \sprintf("%s: [%s]: %s\n", self::class, $this->code, $this->message);
    }
}
