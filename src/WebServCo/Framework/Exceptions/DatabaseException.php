<?php declare(strict_types = 1);

namespace WebServCo\Framework\Exceptions;

final class DatabaseException extends ApplicationException
{
    const CODE = 0;

    protected string $sqlState;

    public function __construct(string $message, \Exception $previous = null)
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

        parent::__construct($message, $code, $previous);
    }

    public function getSqlState(): string
    {
        return $this->sqlState;
    }
}
