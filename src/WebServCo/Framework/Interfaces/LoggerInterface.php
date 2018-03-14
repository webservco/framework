<?php
namespace WebServCo\Framework\Interfaces;

interface LoggerInterface
{
    public function log($level, $message, $context = []);
    public function debug($message, $context = []);
}
