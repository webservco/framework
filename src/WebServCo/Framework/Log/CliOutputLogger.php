<?php

namespace WebServCo\Framework\Log;

class CliOutputLogger extends AbstractOutputLogger implements
    \WebServCo\Framework\Interfaces\OutputLoggerInterface
{
    public function clear()
    {
        $this->output(\WebServCo\Framework\Cli\Ansi::clear(), true);
    }

    public function log($level, $message, $context = [])
    {
        if (!empty($context)) {
            $message = sprintf('[context not outputted] %s', $message);
        }
        $this->output($message, true);
    }
}
