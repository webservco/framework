<?php declare(strict_types = 1);

namespace WebServCo\Framework\Utils;

final class Strings
{

    public static function contains(string $haystack, string $needle, bool $ignoreCase = true): bool
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

        return false !== $function($haystack, $needle);
    }

    public static function endsWith(string $haystack, string $needle): bool
    {
        $functionSubstr = \function_exists('mb_substr')
            ? 'mb_substr'
            : 'substr';
        $functionStrlen = \function_exists('mb_strlen')
            ? 'mb_strlen'
            : 'strlen';
        $check = $functionSubstr($haystack, $functionStrlen($haystack) - $functionStrlen($needle));
        return $check === $needle;
    }

    /**
    * @param mixed $context
    */
    public static function getContextAsString($context): string
    {
        \ob_start();
        \var_dump($context);
        return (string) \ob_get_clean();
    }

    public static function getSlug(string $string): string
    {
        $transliterator = \Transliterator::createFromRules(
            ':: Any-Latin;'
            . ':: NFD;'
            . ':: [:Nonspacing Mark:] Remove;'
            . ':: NFC;'
            . ':: [:Punctuation:] Remove;'
            . ':: Lower();'
            . '[:Separator:] > \'-\''
        );
        if (!($transliterator instanceof \Transliterator)) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException('Transliterator error.');
        }
        return (string) $transliterator->transliterate($string);
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
            $string
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

    public static function stripNonDigits(string $haystack): string
    {
        return (string) \preg_replace("/\D+/", '', $haystack);
    }
}
