<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

abstract class AbstractLogger extends \Psr\Log\AbstractLogger implements \WebServCo\Framework\Interfaces\LoggerInterface
{
    protected function validateLogLevel(string $level): bool
    {
        $levels = LogLevel::getList();

        if (!\in_array($level, $levels, true)) {
            throw new \Psr\Log\InvalidArgumentException('Invalid log level');
        }

        return true;
    }
}
