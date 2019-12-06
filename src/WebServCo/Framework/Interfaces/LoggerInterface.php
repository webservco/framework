<?php
namespace WebServCo\Framework\Interfaces;

interface LoggerInterface
{
    public function clear();
    public function debug($message, $context = []);
    public function error($message, $context = []);
    public function info($message, $context = []);
    public function log($level, $message, $context = []);
}
