<?php declare(strict_types = 1);

namespace WebServCo\Framework\Interfaces;

interface LoggerInterface
{

    public function clear(): bool;

    /**
    * @param mixed $context
    */
    public function debug(string $message, $context = null): bool;

    /**
    * @param mixed $context
    */
    public function error(string $message, $context = null): bool;

    /**
    * @param mixed $context
    */
    public function info(string $message, $context = null): bool;

    /**
    * @param mixed $context
    */
    public function log(string $level, string $message, $context = null): bool;

    /**
    * @param mixed $context
    */
    public function warning(string $message, $context = null): bool;
}
