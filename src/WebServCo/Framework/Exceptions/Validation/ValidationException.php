<?php

namespace WebServCo\Framework\Exceptions\Validation;

class ValidationException extends \WebServCo\Framework\Exceptions\ApplicationException
{
    const CODE = 0;

    public function __construct($message, \Exception $previous = null)
    {
        parent::__construct($message, self::CODE, $previous);
    }
}
