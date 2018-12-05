<?php
namespace WebServCo\Framework\Interfaces;

interface OutputLoggerInterface extends LoggerInterface
{
    public function output($string, $eol = true);
}
