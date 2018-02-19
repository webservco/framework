<?php
namespace WebServCo\Framework\Exceptions;

final class ArrayStorageException extends \Exception
{
    public function __construct($message, $code = 0, Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
    
    public function __toString() {
        return sprintf("%s: [%s]: %s\n", __CLASS__, $this->code, $this->message);
    }
}
