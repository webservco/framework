<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

abstract class AbstractLogger
{

    abstract public function clear(): bool;

    /**
    * @param mixed $context
    */
    abstract public function log(string $level, string $message, $context = null): bool;

    /**
    * @param mixed $context
    */
    public function debug(string $message, $context = null): bool
    {
        return $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
    * @param mixed $context
    */
    public function error(string $message, $context = null): bool
    {
        return $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
    * @param mixed $context
    */
    public function info(string $message, $context = null): bool
    {
        return $this->log(LogLevel::INFO, $message, $context);
    }

    /**
    * @param mixed $context
    */
    public function warning(string $message, $context = null): bool
    {
        return $this->log(LogLevel::WARNING, $message, $context);
    }
}
