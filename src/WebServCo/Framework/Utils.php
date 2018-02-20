<?php
namespace WebServCo\Framework;

final class Utils
{
    public static function isA($array, $key, $defaultValue = null)
    {
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }
}
