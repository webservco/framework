<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

use WebServCo\Framework\Exceptions\LoggerException;

abstract class AbstractFileLogger extends AbstractLogger implements \WebServCo\Framework\Interfaces\FileLoggerInterface
{
    protected string $channel;
    protected string $logDir;
    protected string $logPath;

    public function __construct(string $channel, string $logDir)
    {
        $this->channel = $channel;
        $this->logDir = $logDir;

        if (!$this->logDir) {
            throw new LoggerException(\sprintf('Log directory not set for channel "%s".', $channel));
        }

        if (!\is_writable($this->logDir)) {
            throw new LoggerException(\sprintf('Log directory not writeable: %s.', $this->logDir));
        }
        $this->logPath = \sprintf('%s%s.log', $this->logDir, $this->channel);
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
}
