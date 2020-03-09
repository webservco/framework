<?php
namespace WebServCo\Framework\Log;

use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Interfaces\RequestInterface;

class FileLogger extends AbstractLogger implements \WebServCo\Framework\Interfaces\FileLoggerInterface
{
    protected $channel;
    protected $logDir;
    protected $logPath;
    protected $requestInterface;

    public function __construct($channel, $logDir, RequestInterface $requestInterface)
    {
        $this->channel = $channel;
        $this->logDir = $logDir;

        if (empty($this->logDir)) {
            throw new ApplicationException(sprintf('Log directory not set for channel "%s".', $channel));
        }

        if (!is_readable($this->logDir)) {
            throw new ApplicationException(sprintf('Log directory not readable: %s.', $this->logDir));
        }
        if (!is_writable($this->logDir)) {
            throw new ApplicationException(sprintf('Log directory not writeable: %s.', $this->logDir));
        }
        $this->logPath = sprintf('%s%s.log', $this->logDir, $this->channel);

        $this->requestInterface = $requestInterface;
    }

    public function clear()
    {
        return file_put_contents($this->logPath, null);
    }

    public function getLogDirectory()
    {
        return $this->logDir;
    }

    public function log($level, $message, $context = [])
    {
        $dateTime = new \DateTime();
        $id = $dateTime->format('Ymd.His.u');

        $contextInfo = null;
        if (!empty($context)) {
            $contextAsString = \WebServCo\Framework\Utils\Strings::getContextAsString($context);
            file_put_contents(sprintf('%s/%s.%s.context', $this->logDir, $this->channel, $id), $contextAsString);
            $contextInfo = '[context saved] ';
        }

        $data = sprintf(
            '[%s] %s %s %s%s%s',
            $id,
            $this->requestInterface->getRemoteAddress(),
            $level,
            $contextInfo,
            $message,
            PHP_EOL
        );

        file_put_contents($this->logPath, $data, FILE_APPEND);

        return $id;
    }
}
