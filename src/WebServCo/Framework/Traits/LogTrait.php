<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

/**
* Log simple messages to both file and output.
*/
trait LogTrait
{
    protected \WebServCo\Framework\Interfaces\LoggerInterface $fileLogger;
    protected \WebServCo\Framework\Interfaces\OutputLoggerInterface $outputLogger;

    /**
    * @param mixed $context
    */
    protected function logDebug(string $message, $context = []): void
    {
        $this->outputLogger->debug($message, $context);
        $this->fileLogger->debug($message, $context);
    }

    /**
    * @param mixed $context
    */
    protected function logError(string $message, $context = []): void
    {
        $this->outputLogger->error($message, $context);
        $this->fileLogger->error($message, $context);
    }

    /**
    * @param mixed $context
    */
    protected function logInfo(string $message, $context = []): void
    {
        $this->outputLogger->info($message, $context);
        $this->fileLogger->info($message, $context);
    }

    /**
    * @param mixed $context
    */
    protected function logWarning(string $message, $context = []): void
    {
        $this->outputLogger->warning($message, $context);
        $this->fileLogger->warning($message, $context);
    }
}
