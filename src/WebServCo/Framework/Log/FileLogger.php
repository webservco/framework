<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

class FileLogger extends AbstractFileLogger
{
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

        $dateTime = new \DateTime();
        $id = $dateTime->format('Ymd.His.u');

        $contextInfo = null;
        if ($context) {
            $contextAsString = \WebServCo\Framework\Utils\Strings::getContextAsString($context);
            \file_put_contents(\sprintf('%s/%s.%s.context', $this->logDir, $this->channel, $id), $contextAsString);
            $contextInfo = '[context saved] ';
        }

        $data = \sprintf(
            '[%s] %s %s %s%s%s',
            $id,
            \WebServCo\Framework\Helpers\RequestHelper::getRemoteAddress(),
            $level,
            $contextInfo,
            $message,
            \PHP_EOL,
        );

        \file_put_contents($this->logPath, $data, \FILE_APPEND);
    }
}
