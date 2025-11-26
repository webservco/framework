<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use function array_key_exists;
use function implode;
use function is_array;
use function key;
use function sprintf;

final class ArrayHelper
{
    /**
    * Get a value from an array if it exists, otherwise a specified default value.
    * For multi dimensional arrays please see ArrayStorage.
    *
    * @param array<mixed> $array
    */
    public static function get(array $array, mixed $key, mixed $defaultValue = null): mixed
    {
        return array_key_exists($key, $array)
            ? $array[$key]
            : $defaultValue;
    }

    /**
    * @param array<mixed> $array
    */
    public static function isMultidimensional(array $array): bool
    {
        if (!$array) {
            return false;
        }

        return is_array($array[key($array)]);
    }

    /**
     * @deprecated Use http_build_query instead.
     * @see http_build_query
    * @param array<int|string,int|float|string> $array
    */
    public static function toUrlQueryString(array $array): ?string
    {
        if (!$array) {
            return null;
        }
        $queries = [];
        foreach ($array as $k => $v) {
            $queries[] = sprintf('%s=%s', $k, $v);
        }

        return '?' . implode('&', $queries);
    }
}
