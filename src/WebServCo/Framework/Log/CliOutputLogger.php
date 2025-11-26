<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

use WebServCo\Framework\Cli\Ansi;
use WebServCo\Framework\Interfaces\OutputLoggerInterface;

use function sprintf;

final class CliOutputLogger extends AbstractOutputLogger implements
    OutputLoggerInterface
{
    public function clear(): bool
    {
        $this->output(Ansi::clear(), true);

        return true;
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

        if ($context) {
            $message = sprintf('[context not outputted] %s', $message);
        }
        $this->output($message, true);
    }
}
