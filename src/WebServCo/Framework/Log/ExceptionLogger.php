<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

use WebServCo\Framework\Interfaces\ConfigInterface;
use WebServCo\Framework\Interfaces\LoggerInterface;

/**
* A helper class for logging exceptions to a standard error(CLI) log file.
*/
class ExceptionLogger
{
    protected ConfigInterface $configInterface;
    protected LoggerInterface $fileLogger;

    public function __construct(ConfigInterface $configInterface)
    {
        $this->configInterface = $configInterface;
        $this->fileLogger = new \WebServCo\Framework\Log\FileLogger(
            \WebServCo\Framework\Helpers\PhpHelper::isCli() ? 'errorCLI' : 'error',
            $this->configInterface->get('app/path/log'),
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
