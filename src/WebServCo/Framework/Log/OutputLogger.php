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
    * Logs with an arbitrary level.
    *
    * Uncommon phpdoc syntax used in order to be compatible with \Psr\Log\LoggerInterface
    *
    * @param mixed $level
    * @param string $message
    * @param array<string,mixed> $context
    * @throws \Psr\Log\InvalidArgumentException
    */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ParameterTypeHint.MissingNativeTypeHint
    public function log($level, $message, array $context = []): void
    {
        $this->validateLogLevel($level);

        // SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
        $context = $context;

        $this->output($message, true);
    }
}
