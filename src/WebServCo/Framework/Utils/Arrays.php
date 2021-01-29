<?php

declare(strict_types=1);

namespace WebServCo\Framework\Utils;

final class Arrays
{
    /**
    * @param array<int|string,mixed> $array
    * @return array<int|string,mixed>
    */
    public static function removeEmptyValues(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $array[$key] = self::removeEmptyValues($array[$key]);
            }
            if (empty($array[$key])) {
                unset($array[$key]);
            }
        }
        return $array;
    }

    /**
    * @param array<mixed> $array
    * @param mixed $value
    */
    public static function has(?array $array, $value): bool
    {
        if (!is_array($array)) {
            return false;
        }
        return in_array($value, $array);
    }

    /**
    * Get a value from an array if it exists, otherwise a specified default value.
    * For multi dimensional arrays please see ArrayStorage.
    * @param array<mixed> $array
    * @param mixed $key
    * @param mixed $defaultValue
    * @return mixed
    */
    public static function get(array $array, $key, $defaultValue = false)
    {
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }

    /**
    * @param array<mixed> $array
    */
    public static function isMultidimensional(array $array): bool
    {
        if (empty($array)) {
            return false;
        }
        return is_array($array[key($array)]);
    }

    /**
    * @param array<mixed> $array
    * @return array<mixed>
    */
    public static function nullToEmptyString(array $array): array
    {
        foreach ($array as $key => $value) {
            if (is_null($value)) {
                $array[$key] = '';
            }
        }
        return $array;
    }

    /**
    * Array power set (all element combinations)
    *
    * Original function credit:
    * "PHP Cookbook by David Sklar, Adam Trachtenberg"
    * 4.24. Finding All Element Combinations of an Array
    * https://www.oreilly.com/library/view/php-cookbook/1565926811/ch04s25.html
    * @param array<mixed> $array
    * @return array<mixed>
    */
    public static function powerSet(array $array): array
    {
        $results = [[]]; //initialize by adding the empty set
        foreach ($array as $element) {
            foreach ($results as $combination) {
                array_push($results, array_merge([$element], $combination));
            }
        }
        // sort by number of elements descending
        $array1 = array_map('count', $results);
        array_multisort($array1, SORT_DESC, $results);

        return array_filter($results); // removes empty elements
    }

    /**
    * @param array<mixed> $array
    */
    public static function toUrlQueryString(array $array): ?string
    {
        if (empty($array)) {
            return null;
        }
        $queries = [];
        foreach ($array as $k => $v) {
            $queries[] = sprintf('%s=%s', $k, $v);
        }
        return '?' . implode('&', $queries);
    }
}
