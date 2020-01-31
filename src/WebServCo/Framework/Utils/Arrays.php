<?php
namespace WebServCo\Framework\Utils;

final class Arrays
{
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
    }
}
