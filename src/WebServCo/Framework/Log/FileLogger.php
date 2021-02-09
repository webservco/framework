<?php declare(strict_types = 1);

namespace WebServCo\Framework\Log;

use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Interfaces\RequestInterface;

class FileLogger extends AbstractLogger implements
    \WebServCo\Framework\Interfaces\LoggerInterface,
    \WebServCo\Framework\Interfaces\FileLoggerInterface
{

    protected string $channel;
    protected string $logDir;
    protected string $logPath;
    protected RequestInterface $requestInterface;

    public function __construct(string $channel, string $logDir, RequestInterface $requestInterface)
    {
        $this->channel = $channel;
        $this->logDir = $logDir;

        if (empty($this->logDir)) {
            throw new ApplicationException(\sprintf('Log directory not set for channel "%s".', $channel));
        }

        if (!\is_readable($this->logDir)) {
            throw new ApplicationException(\sprintf('Log directory not readable: %s.', $this->logDir));
        }
        if (!\is_writable($this->logDir)) {
            throw new ApplicationException(\sprintf('Log directory not writeable: %s.', $this->logDir));
        }
        $this->logPath = \sprintf('%s%s.log', $this->logDir, $this->channel);

        $this->requestInterface = $requestInterface;
    }

    public function clear(): bool
    {
        return (bool) \file_put_contents($this->logPath, null);
    }

    public function getLogDirectory(): string
    {
        return $this->logDir;
    }

    /**
    * @param mixed $context
    */
    public function log(string $level, string $message, $context = null): bool
    {
        $dateTime = new \DateTime();
        $id = $dateTime->format('Ymd.His.u');

        $contextInfo = null;
        if (!empty($context)) {
            $contextAsString = \WebServCo\Framework\Utils\Strings::getContextAsString($context);
            \file_put_contents(\sprintf('%s/%s.%s.context', $this->logDir, $this->channel, $id), $contextAsString);
            $contextInfo = '[context saved] ';
        }

        $data = \sprintf(
            '[%s] %s %s %s%s%s',
            $id,
            $this->requestInterface->getRemoteAddress(),
            $level,
            $contextInfo,
            $message,
            \PHP_EOL
        );

        \file_put_contents($this->logPath, $data, \FILE_APPEND);

        return true;
    }
}
