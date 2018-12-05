<?php
namespace WebServCo\Framework;

final class OutputLogger extends AbstractOutputLogger implements \WebServCo\Framework\Interfaces\OutputLoggerInterface
{
    public function clear()
    {
        return false;
    }

    public function log($level, $message, $context = [])
    {
        $this->output($message, true);
    }
}
