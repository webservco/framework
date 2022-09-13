<?php

namespace WebServCo\Framework\Exceptions;

final class DatabaseException extends ApplicationException
{
    const CODE = 0;

    protected $sqlState;

    public function __construct($message, \Exception $previous = null)
    {
        $code = self::CODE;
        if ($previous instanceof \PDOException) {
            if (!empty($previous->errorInfo[1])) {
                $code = $previous->errorInfo[1];
                $this->sqlState = $previous->errorInfo[0];
            }
            if (!empty($previous->errorInfo[2])) {
                // cleaner error message without all the codes.
                $message = $previous->errorInfo[2];
            }
        }
        if ($previous instanceof \mysqli_sql_exception) {
            // https://stackoverflow.com/a/21081034
            $array = (array) $previous;
            if (!empty($array['\0*\0sqlstate'])) {
                $this->sqlState = $array['\0*\0sqlstate'];
            }
        }

        parent::__construct($message, $code, $previous);
    }

    public function getSqlState()
    {
        return $this->sqlState;
    }
}
