<?php
namespace WebServCo\Framework;

abstract class AbstractLogger
{
    abstract public function log($level, $message, $context = []);

    public function debug($message, $context = [])
    {
        return $this->log(\WebServCo\Framework\LogLevel::DEBUG, $message, $context);
    }

    public function error($message, $context = [])
    {
        return $this->log(\WebServCo\Framework\LogLevel::ERROR, $message, $context);
    }
}
