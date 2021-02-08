<?php declare(strict_types = 1);

namespace WebServCo\Framework\Log;

class OutputLogger extends AbstractOutputLogger implements \WebServCo\Framework\Interfaces\OutputLoggerInterface
{
    public function clear(): bool
    {
        return false;
    }

    /**
    * @param string $level
    * @param string $message
    * @param mixed $context
    * @return bool
    */
    public function log(string $level, string $message, $context = null): bool
    {
        return $this->output($message, true);
    }
}
