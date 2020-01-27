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
        $this->output($message, true);
        if (!empty($context)) {
            $this->output('[context not outputted]', true);
        }
    }
}
