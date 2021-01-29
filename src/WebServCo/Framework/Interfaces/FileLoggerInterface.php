<?php
namespace WebServCo\Framework\Interfaces;

interface FileLoggerInterface extends LoggerInterface
{
    public function getLogDirectory(): string;
}
