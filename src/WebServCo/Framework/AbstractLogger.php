<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Interfaces\RequestInterface;

abstract class AbstractLogger
{
    protected $channel;
    protected $logPath;
    protected $requestInterface;

    public function __construct($channel, $logDir, RequestInterface $requestInterface)
    {
        $this->channel = $channel;

        if (!is_readable($logDir)) {
            throw new ApplicationException('Log dir not readable');
        }
        if (!is_writable($logDir)) {
            throw new ApplicationException('Log dir not writeable');
        }
        $this->logPath = sprintf('%s%s.log', $logDir, $this->channel);

        $this->requestInterface = $requestInterface;
    }

    abstract public function log($level, $message, $context = []);

    public function debug($message, $context = [])
    {
        return $this->log(\WebServCo\Framework\LogLevel::DEBUG, $message, $context);
    }

    public function error($message, $context = [])
    {
        return $this->log(\WebServCo\Framework\LogLevel::ERROR, $message, $context);
    }
}
