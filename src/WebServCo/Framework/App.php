<?php declare(strict_types = 1);

namespace WebServCo\Framework;

/*
An example App implementation.
Can be copied or extended by projects.
Custom functionality in this class: add a log when an error occurs.
*/
class App extends Application
{
    /**
     * Handle CLI errors
     *
     * @param array<string,mixed> $errorInfo
     * @return bool
     */
    protected function haltCli(array $errorInfo = []): bool
    {
        $this->logError($errorInfo, true);
        return parent::haltCli($errorInfo);
    }

    /**
     * Handle HTTP errors.
     *
     * @param array<string,mixed> $errorInfo
     * @return bool
     */
    protected function haltHttp(array $errorInfo = []): bool
    {
        $this->logError($errorInfo, false);
        return parent::haltHttp($errorInfo);
    }

    /**
    * @param array<string,mixed> $errorInfo
    * @param bool $isCli
    * @return bool
    */
    protected function logError(array $errorInfo, bool $isCli = false): bool
    {
        $logger = new \WebServCo\Framework\Log\FileLogger(
            sprintf('error%s', $isCli ? 'CLI' : ''),
            $this->config()->get('app/path/log'),
            $this->request()
        );
        $errorMessage = sprintf('Error: %s in %s:%s', $errorInfo['message'], $errorInfo['file'], $errorInfo['line']);
        if ($errorInfo['exception'] instanceof \Exception) {
            $previous = $errorInfo['exception']->getPrevious();
            if ($previous instanceof \Exception) {
                do {
                    $errorMessage .= sprintf(
                        '%sPrevious: %s in %s:%s',
                        PHP_EOL,
                        $previous->getMessage(),
                        $previous->getFile(),
                        $previous->getLine()
                    );
                } while ($previous = $previous->getPrevious());
            }
        }
        return $logger->error($errorMessage, $errorInfo);
    }
}
