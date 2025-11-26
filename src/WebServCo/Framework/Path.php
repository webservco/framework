<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use function sprintf;
use function str_replace;

use const DIRECTORY_SEPARATOR;

final class Path
{
    /**
     * Returns the full path that the framework project is located in.
     */
    public static function get(): string
    {
        return str_replace(
            sprintf('src%sWebServCo%sFramework', DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR),
            '',
            __DIR__,
        );
    }
}
