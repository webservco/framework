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

    public function info($message, $context = [])
    {
        return $this->log(LogLevel::INFO, $message, $context);
    }

    public function warning($message, $context = [])
    {
        return $this->log(LogLevel::WARNING, $message, $context);
    }
}
