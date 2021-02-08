<?php declare(strict_types = 1);

namespace WebServCo\Framework\Log;

abstract class AbstractOutputLogger extends AbstractLogger
{
    public function output(string $string, bool $eol = true): bool
    {
        echo $string;
        if ($eol) {
            echo PHP_EOL;
        }
        return true;
    }
}
