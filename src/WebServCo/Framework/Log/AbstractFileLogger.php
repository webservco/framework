<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

use WebServCo\Framework\Exceptions\LoggerException;

abstract class AbstractFileLogger extends AbstractLogger implements \WebServCo\Framework\Interfaces\FileLoggerInterface
{
    protected string $channel;
    protected string $logDirectory;

    protected string $logPath;

    public function __construct(string $channel, string $logDirectory)
    {
        $this->channel = $channel;

        if ('' === $logDirectory) {
            throw new LoggerException('Log directory not set.');
        }

        // Make sure path contains trailing slash (trim + add back).
        $logDirectory = \rtrim($logDirectory, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;

        $dirResult = $this->createDirectoryIfNotExists($logDirectory);
        if (false === $dirResult) {
            throw new \OutOfBoundsException('Error creating log directory.');
        }

        if (!\is_dir($logDirectory)) {
            throw new LoggerException('Log directory not readable.');
        }

        if (!\is_writable($logDirectory)) {
            throw new LoggerException('Log directory not writable.');
        }

        $this->logDirectory = $logDirectory;

        $this->logPath = \sprintf('%s%s.log', $this->logDirectory, $this->channel);
    }

    public function clear(): bool
    {
        return (bool) \file_put_contents($this->logPath, null);
    }

    public function getLastLine(): int
    {
        $file = new \SplFileObject($this->logPath, 'r');
        $file->seek(\PHP_INT_MAX);
        return $file->key();
    }

    public function getLogDirectory(): string
    {
        return $this->logDirectory;
    }

    protected function createDirectoryIfNotExists(string $directory): bool
    {
        if (\is_dir($directory)) {
            // Directory already exists.
            return true;
        }

        return \mkdir(
            $directory,
            // permissions
            0775,
            // recursive
            true,
            // context
        );
    }
}
