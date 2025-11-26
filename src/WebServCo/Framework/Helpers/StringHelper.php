<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use function function_exists;
use function ob_get_clean;
use function ob_start;
use function preg_replace;
use function var_dump;

final class StringHelper
{
    public static function getContextAsString(mixed $context): string
    {
        ob_start();
        var_dump($context);

        return (string) ob_get_clean();
    }

    public static function isEmpty(string $string): bool
    {
        return $string === '';
    }

    public static function linkify(string $string): string
    {
        return (string) preg_replace(
            "~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~",
            "<a href=\"\\0\">\\0</a>",
            $string,
        );
    }

    public static function startsWith(string $haystack, string $needle, bool $ignoreCase = true): bool
    {
        if ($ignoreCase !== false) {
            $function = function_exists('mb_stripos')
                ? 'mb_stripos'
                : 'stripos';
        } else {
            $function = function_exists('mb_strpos')
                ? 'mb_strpos'
                : 'strpos';
        }

        return $function($haystack, $needle) === 0;
    }
}
