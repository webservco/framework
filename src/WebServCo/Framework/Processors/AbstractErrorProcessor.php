<?php

declare(strict_types=1);

namespace WebServCo\Framework\Processors;

use WebServCo\Framework\Helpers\ErrorMessageHelper;
use WebServCo\Framework\Interfaces\FileLoggerInterface;

/**
* A helper class for logging exceptions to a standard error(CLI) log file.
*/
abstract class AbstractErrorProcessor implements \WebServCo\Framework\Interfaces\ErrorProcessorInterface
{
    protected FileLoggerInterface $fileLogger;

    abstract public function report(\Throwable $exception, ?string $reference = null): bool;

    public function __construct()
    {
        $this->fileLogger = new \WebServCo\Framework\Log\FileLogger(
            \WebServCo\Framework\Helpers\PhpHelper::isCli() ? 'errorCLI' : 'error',
            \WebServCo\Framework\Environment\Config::string('APP_PATH_LOG'),
        );
    }

    public function logException(\Throwable $exception): void
    {
        $message = ErrorMessageHelper::format($exception);
        $this->fileLogger->error($message, ['message' => $message, 'trace' => $exception->getTrace()]);
        $previous = $exception->getPrevious();
        if (!$previous instanceof \Throwable) {
            return;
        }
        do {
            $message = ErrorMessageHelper::format($previous);
            $this->fileLogger->error($message, ['message' => $message, 'trace' => $previous->getTrace()]);
        // phpcs:ignore SlevomatCodingStandard.ControlStructures.AssignmentInCondition.AssignmentInCondition
        } while ($previous = $previous->getPrevious());
    }
}
