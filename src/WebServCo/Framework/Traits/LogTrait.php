<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Interfaces\OutputLoggerInterface;

/**
* Log simple messages to both file and output.
*/
trait LogTrait
{
    protected \Psr\Log\LoggerInterface $fileLogger;
    protected ?OutputLoggerInterface $outputLogger = null;

    /**
    * @param mixed $context
    */
    protected function logDebug(string $message, $context = []): void
    {
        $this->fileLogger->debug($message, $context);
        if (!$this->outputLogger instanceof OutputLoggerInterface) {
            return;
        }
        $this->outputLogger->output($message);
    }

    /**
    * @param mixed $context
    */
    protected function logError(string $message, $context = []): void
    {
        $this->fileLogger->error($message, $context);
        if (!$this->outputLogger instanceof OutputLoggerInterface) {
            return;
        }
        $this->outputLogger->output($message);
    }

    /**
    * @param mixed $context
    */
    protected function logInfo(string $message, $context = []): void
    {
        $this->fileLogger->info($message, $context);
        if (!$this->outputLogger instanceof OutputLoggerInterface) {
            return;
        }
        $this->outputLogger->output($message);
    }

    /**
    * @param mixed $context
    */
    protected function logWarning(string $message, $context = []): void
    {
        $this->fileLogger->warning($message, $context);
        if (!$this->outputLogger instanceof OutputLoggerInterface) {
            return;
        }
        $this->outputLogger->output($message);
    }
}
