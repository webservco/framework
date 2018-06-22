<?php
namespace WebServCo\Framework\Utils;

final class Strings
{
    public static function startsWith($haystack, $needle, $ignoreCase = true)
    {
        if (false !== $ignoreCase) {
            $function = function_exists('mb_stripos') ? 'mb_stripos' : 'stripos';
        } else {
            $function = function_exists('mb_strpos') ? 'mb_strpos' : 'strpos';
        }

        return 0 === $function($haystack, $needle);
    }
}
