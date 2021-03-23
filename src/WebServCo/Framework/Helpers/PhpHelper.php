<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

final class PhpHelper
{
    /**
     * Checks if interface type is CLI.
     */
    public static function isCli(): bool
    {
        return 'cli' === \PHP_SAPI;
    }
}
