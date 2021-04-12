<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

use WebServCo\Framework\Interfaces\LoggerInterface;

/**
* A helper class for logging exceptions to a standard error(CLI) log file.
*/
class ExceptionLogger
{
    protected LoggerInterface $fileLogger;

    public function __construct()
    {
        $this->fileLogger = new \WebServCo\Framework\Log\FileLogger(
            \WebServCo\Framework\Helpers\PhpHelper::isCli() ? 'errorCLI' : 'error',
            \WebServCo\Framework\EnvironmentConfiguration\Config::getString('APP_PATH_LOG'),
        );
    }

    public function getFormattedMessage(\Throwable $exception): string
    {
        return \sprintf(
            'Error: %s in %s:%s.',
            $exception->getMessage(),
            $exception->getFile(),
            $exception->getLine(),
        );
    }

    public function log(\Throwable $exception): void
    {
        $errorMessage = $this->getFormattedMessage($exception);
        $errorInfo = \WebServCo\Framework\ErrorHandler::getErrorInfo($exception);
        $this->fileLogger->error($errorMessage, $errorInfo);
    }
}
