<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

/**
* Helper for all available framework libraries.
*/
abstract class AbstractLibraryHelper
{
    protected static function loadLibraryHelper(string $name): void
    {
        $path = \sprintf(
            '%ssrc%sWebServCo%sFramework%sLibraryHelpers%s%sHelper.php',
            \WebServCo\Framework\Path::get(),
            \DIRECTORY_SEPARATOR,
            \DIRECTORY_SEPARATOR,
            \DIRECTORY_SEPARATOR,
            \DIRECTORY_SEPARATOR,
            $name,
        );

        if (!\is_readable($path)) {
            throw new \WebServCo\Framework\Exceptions\LibraryException(
                \sprintf('Helper for %s Library not found.', $name),
            );
        }
        require $path;
    }
}
