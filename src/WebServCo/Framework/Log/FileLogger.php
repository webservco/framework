<?php
namespace WebServCo\Framework\Log;

use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Interfaces\RequestInterface;

final class FileLogger extends AbstractLogger implements \WebServCo\Framework\Interfaces\FileLoggerInterface
{
    protected $channel;
    protected $logDir;
    protected $logPath;
    protected $requestInterface;

    public function __construct($channel, $logDir, RequestInterface $requestInterface)
    {
        $this->channel = $channel;
        $this->logDir = $logDir;

        if (!is_readable($this->logDir)) {
            throw new ApplicationException('Log directory not readable');
        }
        if (!is_writable($this->logDir)) {
            throw new ApplicationException('Log directory not writeable');
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
}
