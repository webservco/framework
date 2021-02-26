<?php

declare(strict_types=1);

namespace WebServCo\Framework;

final class Path
{
    /**
     * Returns the full path that the framework project is located in.
     */
    public static function get(): string
    {
        return \str_replace(
            \sprintf('src%sWebServCo%sFramework', \DIRECTORY_SEPARATOR, \DIRECTORY_SEPARATOR),
            '',
            __DIR__
        );
    }
}
