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
