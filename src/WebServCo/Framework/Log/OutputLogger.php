<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

class OutputLogger extends AbstractOutputLogger implements \WebServCo\Framework\Interfaces\OutputLoggerInterface
{

    public function clear(): bool
    {
        return false;
    }

    /**
    * @param mixed $context
    */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    public function log(string $level, string $message, $context = null): bool
    {
        return $this->output($message, true);
    }
}
