<?php
namespace WebServCo\Framework;

final class OutputLogger extends AbstractLogger implements \WebServCo\Framework\Interfaces\LoggerInterface
{
    public function log($level, $message, $context = [])
    {
        echo $message;
    }
}
