<?php
namespace WebServCo\Framework\Exceptions;

class HttpException extends \Exception
{
    const CODE = 400;

    public function __construct($message, $code = self::CODE, \Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString()
    {
        return sprintf("%s: [%s]: %s\n", __CLASS__, $this->code, $this->message);
    }
}
