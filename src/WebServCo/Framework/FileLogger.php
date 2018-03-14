<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Interfaces\RequestInterface;
use WebServCo\Framework\Exceptions\ApplicationException;

final class FileLogger implements \WebServCo\Framework\Interfaces\LoggerInterface
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

        if (!($requestInterface instanceof RequestInterface)) {
            throw new ApplicationException('Missing RequestInterface');
        }
        $this->requestInterface = $requestInterface;
    }

    public function debug($message, $context = [])
    {
        return $this->log(\WebServCo\Framework\LogLevel::DEBUG, $message, $context);
    }

    public function log($level, $message, $context = [])
    {
        $data = sprintf(
            '[%s] [%s] [%s] %s%s',
            date('Y-m-d H:i:s'),
            $this->requestInterface->getRemoteAddress(),
            $level,
            $message,
            PHP_EOL
        );
        if (!empty($context)) {
            $data .= sprintf('%s%s', var_export($context, true), PHP_EOL);
        }
        return file_put_contents($this->logPath, $data, FILE_APPEND);
    }

    public function clear()
    {
        return file_put_contents($this->logPath, null);
    }
}
