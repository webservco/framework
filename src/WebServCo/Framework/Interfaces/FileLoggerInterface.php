<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface FileLoggerInterface extends LoggerInterface
{
    public function getLastLine(): int;
}
