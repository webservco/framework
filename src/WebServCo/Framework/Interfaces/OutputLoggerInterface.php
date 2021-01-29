<?php
namespace WebServCo\Framework\Interfaces;

interface OutputLoggerInterface extends LoggerInterface
{
    public function output(string $string, bool $eol = true): bool;
}
