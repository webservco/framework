<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

use OutOfBoundsException;
use SplFileObject;
use WebServCo\Framework\Exceptions\LoggerException;
use WebServCo\Framework\Interfaces\FileLoggerInterface;

use function file_put_contents;
use function is_dir;
use function is_writable;
use function mkdir;
use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;
use const PHP_INT_MAX;

abstract class AbstractFileLogger extends AbstractLogger implements FileLoggerInterface
{
    protected string $logDirectory;

    protected string $logPath;

    public function __construct(protected string $channel, string $logDirectory)
    {
        if ($logDirectory === '') {
            throw new LoggerException('Log directory not set.');
        }

        // Make sure path contains trailing slash (trim + add back).
        $logDirectory = rtrim($logDirectory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        $dirResult = $this->createDirectoryIfNotExists($logDirectory);
        if ($dirResult === false) {
            throw new OutOfBoundsException('Error creating log directory.');
        }

        if (!is_dir($logDirectory)) {
            throw new LoggerException('Log directory not readable.');
        }

        if (!is_writable($logDirectory)) {
            throw new LoggerException('Log directory not writable.');
        }

        $this->logDirectory = $logDirectory;

        $this->logPath = sprintf('%s%s.log', $this->logDirectory, $this->channel);
    }

    public function clear(): bool
    {
        return (bool) file_put_contents($this->logPath, null);
    }

    public function getLastLine(): int
    {
        $file = new SplFileObject($this->logPath, 'r');
        $file->seek(PHP_INT_MAX);

        return $file->key();
    }

    public function getLogDirectory(): string
    {
        return $this->logDirectory;
    }

    protected function createDirectoryIfNotExists(string $directory): bool
    {
        if (is_dir($directory)) {
            // Directory already exists.
            return true;
        }

        return mkdir(
            $directory,
            // permissions
            0775,
            // recursive
            true,
            // context
        );
    }
}
