<?php

declare(strict_types=1);

namespace WebServCo\Framework\Processors;

use WebServCo\Framework\Helpers\ErrorMessageHelper;
use WebServCo\Framework\Interfaces\LoggerInterface;

/**
* A helper class for logging exceptions to a standard error(CLI) log file.
*/
class ErrorProcessor
{
    protected LoggerInterface $fileLogger;

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

    public function logRequest(\WebServCo\Framework\Interfaces\RequestInterface $requestInterface): bool
    {
        // \Psr\Log\LoggerInterface requires $context to be an array
        $this->fileLogger->debug(
            'RequestInterface debug',
            [
                'args' => $requestInterface->getArgs(),
                'body' => $requestInterface->getBody(),
                'contentType' => $requestInterface->getContentType(),
                'data' => $requestInterface->getData(),
                'method' => $requestInterface->getMethod(),
                'query' => $requestInterface->getQuery(),
                'remoteAddress' => $requestInterface->getRemoteAddress(),
                'url' => $requestInterface->getUrl(),
                'userAgent' => $requestInterface->getUserAgent(),
            ],
        );
        return true;
    }
}