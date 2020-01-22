<?php
namespace WebServCo\Framework\Log;

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
            $contextAsString = \WebServCo\Framework\Utils\Strings::getContextAsString($context);
            $this->output(sprintf('Context: %s%s', $contextAsString, PHP_EOL), true);
        }
    }
}
