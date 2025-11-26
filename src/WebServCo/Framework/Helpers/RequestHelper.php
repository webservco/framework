<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use function array_diff;
use function array_key_exists;
use function array_map;
use function count;
use function explode;
use function gethostbyname;
use function php_uname;
use function preg_replace;
use function str_replace;
use function strlen;
use function strncasecmp;
use function strpos;
use function strrev;
use function substr;
use function trim;

final class RequestHelper
{
    /**
    * @return array<int,string>
    */
    public static function explode(string $string): array
    {
        if (strpos($string, '?') !== false) {
            return explode('?', $string, 2);
        }

        if (strpos($string, '&') !== false) {
            return explode('&', $string, 2);
        }

        return [$string, ''];
    }

    /**
    * @return array<string, string|null>
    */
    public static function format(string $string): array
    {
        $data = [];
        $parts = self::split($string);
        $num = count($parts);
        for ($position = 0; $position < $num; $position += 2) {
            if (!array_key_exists($position, $parts)) {
                // Prevents "Notice: Undefined offset: 2." in request like "&lang=/'/"
                continue;
            }
            $data[$parts[$position]] = $position === $num - 1
                ? null
                : $parts[$position + 1];
        }

        return $data;
    }

    public static function getRemoteAddress(): string
    {
        if (PhpHelper::isCli()) {
            return gethostbyname(php_uname('n'));
        }

        if (array_key_exists('REMOTE_ADDR', $_SERVER)) {
            return self::sanitizeString($_SERVER['REMOTE_ADDR']);
        }

        return '';
    }

    /**
    * @param array<int,string> $suffixes
    * @return array<int,string>
    */
    public static function parse(string $string, string $path, string $filename, array $suffixes): array
    {
        $pathLen = strlen($path);
        if (strncasecmp($path, $string, $pathLen) === 0) {
            $string = substr($string, $pathLen);
        }
        $filenameLen = strlen($filename);
        if (strncasecmp($filename, $string, $filenameLen) === 0) {
            $string = substr($string, $filenameLen);
        }
        [$target, $query] = self::explode($string);
        [$target, $suffix] = self::removeSuffix(
            self::transform($target),
            $suffixes,
        );
        $query = self::transform($query);

        return [$target, $query, $suffix];
    }

    /**
    * @param array<int,string> $suffixes
    * @return array<int,string>
    */
    public static function removeSuffix(string $string, array $suffixes = []): array
    {
        if ($suffixes) {
            $stringRev = strrev($string);
            foreach ($suffixes as $suffix) {
                $suffixRev = strrev($suffix);
                $suffixLen = strlen($suffix);
                if (strncasecmp($suffixRev, $stringRev, $suffixLen) === 0) {
                    return [strrev(substr($stringRev, $suffixLen)), $suffix];
                }
            }
        }

        return [$string, ''];
    }

    public static function sanitizeString(string $string): string
    {
        // Strip tags, optionally strip or encode special characters.
        /**
         * FILTER_SANITIZE_STRING is deprecated since PHP 8.1.
         *
         * $string = \filter_var($string, \FILTER_SANITIZE_STRING);
         * Use a polyfill instead.
         * Source: https://stackoverflow.com/a/69207369
         */
        $string = preg_replace('/\x00|<[^>]*>?/', '', $string);
        $string = str_replace(["'", '"'], ['&#39;', '&#34;'], $string);

        $unwanted = [
            "`",
            //"'",
            //'"',
            "\b",
            "\n",
            "\r",
            "\t",
            //"?",
            //"!",
            //"~",
            //"#",
            //"^",
            //"&",
            //"*",
            //"=",
            //"[",
            //"]",
            //":",
            //";",
            //",",
            //"|",
            "\\",
            //"{",
            //"}",
            //"(",
            //")",
            "\$",
        ];
        $string = str_replace($unwanted, '', (string) $string);

        return $string;
    }

    /**
    * @return array<int,string>
    */
    public static function split(string $string): array
    {
        $parts = explode('/', $string);
        $parts = array_map('urldecode', $parts);

        return array_diff($parts, ['']);
    }

    public static function transform(string $string): string
    {
        $string = str_replace(['?', '&', '=', '//'], ['', '/', '/', '/0/'], $string);

        return trim($string, ' /');
    }
}
