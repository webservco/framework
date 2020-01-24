<?php
namespace WebServCo\Framework;

/*
An example App implementation.
Can be copied or extended by projects.
Add a log when an error occurs.
*/
class App extends Application
{
    /**
     * Handle HTTP errors.
     */
    protected function haltHttp($errorInfo = [])
    {
        $this->logError($errorInfo, false);
        return parent::haltHttp($errorInfo);
    }

    /**
     * Handle CLI errors
     */
    protected function haltCli($errorInfo = [])
    {
        $this->logError($errorInfo, true);
        return parent::haltCli($errorInfo);
    }

    protected function logError($errorInfo, $isCli = false)
    {
        $logger = new FileLogger(
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
        $logger->error($errorMessage, $errorInfo);
    }
}
