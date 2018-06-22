<?php
namespace WebServCo\Framework\Utils;

final class Arrays
{
    public static function get($array, $key, $defaultValue = false)
    {
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }
}
