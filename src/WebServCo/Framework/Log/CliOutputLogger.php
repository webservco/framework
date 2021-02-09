<?php declare(strict_types = 1);

namespace WebServCo\Framework\Log;

class CliOutputLogger extends AbstractOutputLogger implements
    \WebServCo\Framework\Interfaces\OutputLoggerInterface
{

    public function clear(): bool
    {
        return $this->output(\WebServCo\Framework\Cli\Ansi::clear(), true);
    }

    /**
    * @param mixed $context
    */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function log(string $level, string $message, $context = null): bool
    {
        if (!empty($context)) {
            $message = \sprintf('[context not outputted] %s', $message);
        }
        return $this->output($message, true);
    }
}
