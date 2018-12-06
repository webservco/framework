<?php
namespace WebServCo\Framework\Log;

abstract class AbstractLogger
{
    abstract public function clear();

    abstract public function log($level, $message, $context = []);

    public function debug($message, $context = [])
    {
        return $this->log(LogLevel::DEBUG, $message, $context);
    }

    public function error($message, $context = [])
    {
        return $this->log(LogLevel::ERROR, $message, $context);
    }
}
