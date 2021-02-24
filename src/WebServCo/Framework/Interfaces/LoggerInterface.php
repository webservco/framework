<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface LoggerInterface extends \Psr\Log\LoggerInterface
{
    public function clear(): bool;
}
