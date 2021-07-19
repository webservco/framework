<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface ErrorProcessorInterface
{
    public function logException(\Throwable $exception): void;

    public function report(\Throwable $exception, ?string $reference = null): bool;
}
