<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

use Psr\Log\AbstractLogger as PsrAbstractLogger;
use Psr\Log\InvalidArgumentException;
use WebServCo\Framework\Interfaces\LoggerInterface;

use function in_array;

abstract class AbstractLogger extends PsrAbstractLogger implements LoggerInterface
{
    protected function validateLogLevel(string $level): bool
    {
        $levels = LogLevel::getList();

        if (!in_array($level, $levels, true)) {
            throw new InvalidArgumentException('Invalid log level');
        }

        return true;
    }
}
