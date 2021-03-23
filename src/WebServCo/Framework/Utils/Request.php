<?php

declare(strict_types=1);

namespace WebServCo\Framework\Utils;

final class Request
{

    /**
    * @return array<int,string>
    */
    public static function explode(string $string): array
    {
        if (false !== \strpos($string, '?')) {
            return \explode('?', $string, 2);
        }

        if (false !== \strpos($string, '&')) {
            return \explode('&', $string, 2);
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
        $num = \count($parts);
        for ($position = 0; $position < $num; $position += 2) {
            $data[$parts[$position]] = $position === $num - 1
                ? null
                :
            $parts[$position + 1];
        }
        return $data;
    }

    /**
    * @param array<int,string> $suffixes
    * @return array<int,string>
    */
    public static function parse(string $string, string $path, string $filename, array $suffixes): array
    {
        $pathLen = \strlen($path);
        if (0 === \strncasecmp($path, $string, $pathLen)) {
            $string = \substr($string, $pathLen);
        }
        $filenameLen = \strlen($filename);
        if (0 === \strncasecmp($filename, $string, $filenameLen)) {
            $string = \substr($string, $filenameLen);
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
        if (\is_array($suffixes)) {
            $stringRev = \strrev($string);
            foreach ($suffixes as $suffix) {
                $suffixRev = \strrev($suffix);
                $suffixLen = \strlen($suffix);
                if (0 === \strncasecmp($suffixRev, $stringRev, $suffixLen)) {
                    return [\strrev(\substr($stringRev, $suffixLen)), $suffix];
                }
            }
        }
        return [$string, ''];
    }

    public static function sanitizeString(string $string): string
    {
        // Strip tags, optionally strip or encode special characters.
        $string = \filter_var($string, \FILTER_SANITIZE_STRING);
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
        $string = \str_replace($unwanted, '', (string) $string);
        return $string;
    }

    /**
    * @return array<int,string>
    */
    public static function split(string $string): array
    {
        $parts = \explode('/', $string);
        $parts = \array_map('urldecode', $parts);
        return \array_diff($parts, ['']);
    }

    public static function transform(string $string): string
    {
        $string = \str_replace(['?', '&', '=', '//'], ['', '/', '/', '/0/'], $string);
        return \trim($string, ' /');
    }
}
