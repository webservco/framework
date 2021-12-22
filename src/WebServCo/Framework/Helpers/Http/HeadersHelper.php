<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers\Http;

class HeadersHelper
{
    /**
    * Convert headers array (one header per line) into parsed array
    *
    * Array key will contain the header name
    * Array value will be an array containing all the header values.
    * "HTTP" line is ignored.
    *
    * @param array<int,string> $responseHeadersArray
    * @return array<string,mixed>
    */
    public static function parseArray(array $responseHeadersArray = [], bool $lowercaseKey = true): array
    {
        $headers = [];

        foreach ($responseHeadersArray as $index => $line) {
            if ('HTTP' === \substr($line, 0, 4)) {
                continue; /* we'll get the status code elsewhere */
            }
            $parts = \explode(': ', $line, 2);
            if (!isset($parts[1])) {
                continue; // invalid header (missing colon)
            }
            [$key, $value] = $parts;
            if ($lowercaseKey) {
                $key = \strtolower($key);
            }
            if (isset($headers[$key])) {
                if (!\is_array($headers[$key])) {
                    $headers[$key] = [$headers[$key]];
                }
                // check cookies
                if ('Set-Cookie' === $key) {
                    $parts = \explode('=', $value, 2);
                    $cookieName = $parts[0];
                    if (\is_array($headers[$key])) {
                        foreach ($headers[$key] as $cookieIndex => $existingCookie) {
                            // check if we already have a cookie with the same name
                            if (0 !== \mb_stripos($existingCookie, $cookieName)) {
                                continue;
                            }

                            // remove previous cookie with the same name
                            unset($headers[$key][$cookieIndex]);
                        }
                    }
                }
                $headers[$key][] = \trim($value);
                $headers[$key] = \array_values((array) $headers[$key]); // re-index array
            } else {
                $headers[$key][] = \trim($value);
            }
        }
        return $headers;
    }

    /**
    * Convert headers string to array.
    *
    * Does not support multi line headers.
    * "HTTP" line is ignored.
    *
    * @return array<string,mixed>
    */
    public static function parseString(string $headers, bool $lowercaseKey = true): array
    {
        $array = \explode(\PHP_EOL, $headers);
        return self::parseArray($array, $lowercaseKey);
    }
}
