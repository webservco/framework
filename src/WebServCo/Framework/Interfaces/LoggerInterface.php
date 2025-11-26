<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

use Psr\Log\LoggerInterface as PsrLoggerInterface;

interface LoggerInterface extends PsrLoggerInterface
{
    public function clear(): bool;
}
