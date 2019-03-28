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

    public static function endsWith($haystack, $needle)
    {
        $functionSubstr = function_exists('mb_substr') ? 'mb_substr' : 'substr';
        $functionStrlen = function_exists('mb_strlen') ? 'mb_strlen' : 'strlen';
        $check = $functionSubstr($haystack, $functionStrlen($haystack) - ($functionStrlen($needle)));
        return $check == $needle;
    }

    public static function contains($haystack, $needle, $ignoreCase = true)
    {
        if (false !== $ignoreCase) {
            $function = function_exists('mb_stripos') ? 'mb_stripos' : 'stripos';
        } else {
            $function = function_exists('mb_strpos') ? 'mb_strpos' : 'strpos';
        }

        return false !== $function($haystack, $needle);
    }

    public static function stripNonDigits($haystack)
    {
        return preg_replace("/\D+/", '', $haystack);
    }
}
