<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

class StringHelper
{
    /**
    * @param mixed $context
    */
    public static function getContextAsString($context): string
    {
        \ob_start();
        \var_dump($context);
        return (string) \ob_get_clean();
    }

    public static function isEmpty(string $string): bool
    {
        if ('' === $string) {
            return true;
        }

        return false;
    }

    public static function linkify(string $string): string
    {
        return (string) \preg_replace(
            "~[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]~",
            "<a href=\"\\0\">\\0</a>",
            $string,
        );
    }

    public static function startsWith(string $haystack, string $needle, bool $ignoreCase = true): bool
    {
        if (false !== $ignoreCase) {
            $function = \function_exists('mb_stripos')
                ? 'mb_stripos'
                : 'stripos';
        } else {
            $function = \function_exists('mb_strpos')
                ? 'mb_strpos'
                : 'strpos';
        }

        return 0 === $function($haystack, $needle);
    }
}
