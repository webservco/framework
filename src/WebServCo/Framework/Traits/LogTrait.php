<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use Psr\Log\LoggerInterface;
use WebServCo\Framework\Interfaces\OutputLoggerInterface;

/**
* Log simple messages to both file and output.
*/
trait LogTrait
{
    protected LoggerInterface $fileLogger;
    protected ?OutputLoggerInterface $outputLogger = null;

    protected function logDebug(string $message, mixed $context = []): void
    {
        $this->fileLogger->debug($message, $context);
        if (!$this->outputLogger instanceof OutputLoggerInterface) {
            return;
        }
        $this->outputLogger->output($message);
    }

    protected function logError(string $message, mixed $context = []): void
    {
        $this->fileLogger->error($message, $context);
        if (!$this->outputLogger instanceof OutputLoggerInterface) {
            return;
        }
        $this->outputLogger->output($message);
    }

    protected function logInfo(string $message, mixed $context = []): void
    {
        $this->fileLogger->info($message, $context);
        if (!$this->outputLogger instanceof OutputLoggerInterface) {
            return;
        }
        $this->outputLogger->output($message);
    }

    protected function logWarning(string $message, mixed $context = []): void
    {
        $this->fileLogger->warning($message, $context);
        if (!$this->outputLogger instanceof OutputLoggerInterface) {
            return;
        }
        $this->outputLogger->output($message);
    }
}
