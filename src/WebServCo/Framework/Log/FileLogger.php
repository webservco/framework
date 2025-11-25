<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

class FileLogger extends AbstractFileLogger
{
    /**
    * Logs with an arbitrary level.
    *
    * Uncommon phpdoc syntax used in order to be compatible with \Psr\Log\LoggerInterface
    * \Psr\Log\LoggerInterface requires $context to be an array
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
            $contextDirectory = \sprintf('%s/context-%s', $this->logDirectory, $this->channel);
            // Make sure path contains trailing slash (trim + add back).
            $contextDirectory = \rtrim($contextDirectory, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
            $dirResult = $this->createDirectoryIfNotExists($contextDirectory);
            if (false === $dirResult) {
                throw new \OutOfBoundsException('Error creating log directory.');
            }

            $contextAsString = \WebServCo\Framework\Helpers\StringHelper::getContextAsString($context);

            \file_put_contents(
                \sprintf('%s%s.context', $contextDirectory, $id),
                $contextAsString,
            );
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

        $fileResult = \file_put_contents($this->logPath, $data, \FILE_APPEND);
        if (false === $fileResult) {
            throw new \OutOfBoundsException('Error writing log file.');
        }
    }
}
