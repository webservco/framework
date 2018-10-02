<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Interfaces\RequestInterface;

final class FileLogger extends AbstractLogger implements \WebServCo\Framework\Interfaces\LoggerInterface
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
