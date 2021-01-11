<?php
namespace WebServCo\Framework\Interfaces;

interface LoggerInterface
{
    public function clear() : bool;

    /**
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function debug(string $message, $context = null) : bool;

    /**
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function error(string $message, $context = null) : bool;

    /**
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function info(string $message, $context = null) : bool;

    /**
    * @param string $level
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function log(string $level, string $message, $context = null) : bool;

    /**
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function warning(string $message, $context = null) : bool;
}
