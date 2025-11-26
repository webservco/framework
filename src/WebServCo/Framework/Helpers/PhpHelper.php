<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use const PHP_SAPI;

final class PhpHelper
{
    /**
     * Checks if interface type is CLI.
     */
    public static function isCli(): bool
    {
        return PHP_SAPI === 'cli';
    }
}
