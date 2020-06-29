<?php
namespace WebServCo\Framework\Utils;

final class Arrays
{
    /**
    * @param array[] $array
    * @param mixed $value
    */
    public function has(array $array, $value) : bool
    {
        if (!is_array($array)) {
            return false;
        }
        return in_array($value, $array);
    }

    /**
    * @param array[] $array
    * @param mixed $key
    * @param mixed $defaultValue
    */
    public static function get(array $array, $key, $defaultValue = false) : bool
    {
        if (!is_array($array)) {
            return $defaultValue;
        }
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }

    /**
    * @param array[] $array
    */
    public static function isMultidimensional(array $array) : bool
    {
        if (empty($array)) {
            return false;
        }
        return is_array($array[key($array)]);
    }

    /**
    * @param array<mixed> $array
    * @return array[]
    */
    public static function nullToEmptyString(array $array) : array
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
    * @param array[] $array
    * @return array[]
    */
    public static function powerSet(array $array) : array
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
    public static function toUrlQueryString(array $array) : ?string
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
