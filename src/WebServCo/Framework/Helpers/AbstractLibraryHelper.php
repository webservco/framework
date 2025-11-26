<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Exceptions\LibraryException;
use WebServCo\Framework\Path;

use function is_readable;
use function sprintf;

use const DIRECTORY_SEPARATOR;

/**
* Helper for all available framework libraries.
*/
abstract class AbstractLibraryHelper
{
    protected static function loadLibraryHelper(string $name): void
    {
        $path = sprintf(
            '%ssrc%sWebServCo%sFramework%sLibraryHelpers%s%sHelper.php',
            Path::get(),
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            DIRECTORY_SEPARATOR,
            $name,
        );

        if (!is_readable($path)) {
            throw new LibraryException(
                sprintf('Helper for %s Library not found.', $name),
            );
        }
        require $path;
    }
}
