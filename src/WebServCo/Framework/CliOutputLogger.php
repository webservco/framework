<?php
namespace WebServCo\Framework;

final class CliOutputLogger extends AbstractOutputLogger implements
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
            $this->output(var_export($context, true), true);
        }
    }
}
