<?php declare(strict_types = 1);

namespace WebServCo\Framework\Exceptions;

class UploadException extends ApplicationException
{
    const CODE = -1;

    public function __construct(int $code = self::CODE, \Exception $previous = null)
    {
        $message = \WebServCo\Framework\Files\Upload\Codes::getMessage($code);
        parent::__construct($message, $code, $previous);
    }
}
