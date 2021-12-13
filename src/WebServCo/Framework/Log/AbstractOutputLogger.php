<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

abstract class AbstractOutputLogger extends AbstractLogger
{
    public function output(string $string, bool $eol = true): void
    {
        echo $string;
        // phpcs:ignore SlevomatCodingStandard.ControlStructures.EarlyExit.EarlyExitNotUsed
        if ($eol) {
            echo \PHP_EOL;
        }
    }
}
