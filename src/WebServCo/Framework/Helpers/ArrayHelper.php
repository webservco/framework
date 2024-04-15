<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

class ArrayHelper
{
    /**
    * Get a value from an array if it exists, otherwise a specified default value.
    * For multi dimensional arrays please see ArrayStorage.
    *
    * @param array<mixed> $array
    * @param mixed $key
    * @param mixed $defaultValue
    * @return mixed
    */
    public static function get(array $array, $key, $defaultValue = null)
    {
        return \array_key_exists($key, $array)
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
        return \is_array($array[\key($array)]);
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
            $queries[] = \sprintf('%s=%s', $k, $v);
        }
        return '?' . \implode('&', $queries);
    }
}
