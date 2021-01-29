<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

abstract class AbstractLogger
{
    abstract public function clear(): bool;

    /**
    * @param string $level
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    abstract public function log(string $level, string $message, $context = null): bool;

    /**
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function debug(string $message, $context = null): bool
    {
        return $this->log(LogLevel::DEBUG, $message, $context);
    }

    /**
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function error(string $message, $context = null): bool
    {
        return $this->log(LogLevel::ERROR, $message, $context);
    }

    /**
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function info(string $message, $context = null): bool
    {
        return $this->log(LogLevel::INFO, $message, $context);
    }

    /**
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function warning(string $message, $context = null): bool
    {
        return $this->log(LogLevel::WARNING, $message, $context);
    }
}
