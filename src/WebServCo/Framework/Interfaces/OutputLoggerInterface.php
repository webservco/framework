<?php declare(strict_types = 1);

namespace WebServCo\Framework\Interfaces;

interface OutputLoggerInterface extends LoggerInterface
{

    public function output(string $string, bool $eol = true): bool;
}
