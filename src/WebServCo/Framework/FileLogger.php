<?php
namespace WebServCo\Framework;

final class FileLogger extends AbstractLogger implements \WebServCo\Framework\Interfaces\LoggerInterface
{
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
