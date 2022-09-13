<?php

namespace WebServCo\Framework;

final class RequestUtils
{
    public static function explode($string)
    {
        if (false !== strpos($string, '?')) {
            return explode('?', $string, 2);
        } elseif (false !== strpos($string, '&')) {
            return explode('&', $string, 2);
        }
        return [$string, null];
    }

    public static function transform($string)
    {
        $string = str_replace(['?','&','=','//'], ['','/','/','/0/'], $string);
        return trim($string, ' /');
    }

    public static function format($string)
    {
        $data = [];
        $parts = self::split($string);
        $num = count($parts);
        for ($position = 0; $position < $num; $position += 2) {
            $data[$parts[$position]] = $position == $num - 1 ? null :
            $parts[$position + 1];
        }
        return $data;
    }

    public static function split($string)
    {
        $parts = explode('/', $string);
        $parts = array_map('urldecode', $parts);
        return array_diff($parts, ['']);
    }

    public static function removeSuffix($string, $suffixes = [])
    {
        if (is_array($suffixes)) {
            $stringRev = strrev($string);
            foreach ($suffixes as $suffix) {
                $suffixRev = strrev($suffix);
                $suffixLen = strlen($suffix);
                if (0 === strncasecmp($suffixRev, $stringRev, $suffixLen)) {
                    return [strrev(substr($stringRev, $suffixLen)), $suffix];
                }
            }
        }
        return [$string, null];
    }

    public static function sanitizeString($string)
    {
        // Strip tags, optionally strip or encode special characters.
        $string = filter_var($string, FILTER_SANITIZE_STRING);
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
            "\$"
        ];
        $string = str_replace($unwanted, '', (string) $string);
        return $string;
    }

    public static function parse($string, $path, $filename, $suffixes)
    {
        $pathLen = strlen($path);
        if (0 === strncasecmp($path, $string, $pathLen)) {
            $string = substr($string, $pathLen);
        }
        $filenameLen = strlen($filename);
        if (0 === strncasecmp($filename, $string, $filenameLen)) {
            $string = substr($string, $filenameLen);
        }
        list($target, $query) = self::explode($string);
        list($target, $suffix) = self::removeSuffix(
            self::transform($target),
            $suffixes
        );
        $query = self::transform($query);
        return [$target, $query, $suffix];
    }
}
