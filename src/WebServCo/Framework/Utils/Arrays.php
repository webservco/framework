<?php
namespace WebServCo\Framework\Utils;

final class Arrays
{
    public function has($array, $value)
    {
        if (!is_array($array)) {
            return false;
        }
        return in_array($value, $array);
    }

    public static function get($array, $key, $defaultValue = false)
    {
        if (!is_array($array)) {
            return $defaultValue;
        }
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }

    public static function isMultidimensional($array = [])
    {
        if (empty($array)) {
            return false;
        }
        return is_array($array[key($array)]);
    }

    public static function nullToEmptyString($array = [])
    {
        foreach ($array as $key => $value) {
            if (is_null($value)) {
                $array[$key] = '';
            }
        }
        return $array;
    }

    public static function toUrlQueryString($array = [])
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
