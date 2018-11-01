<?php
namespace WebServCo\Framework\Exceptions;

final class DatabaseException extends ApplicationException
{
    const CODE = 0;

    public function __construct($message, $code = self::CODE, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return sprintf("%s: [%s]: %s\n", __CLASS__, $this->code, $this->message);
    }
}
