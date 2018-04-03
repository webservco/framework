<?php
namespace WebServCo\Framework;

final class Utils
{
    public static function arrayKey($key, $array, $defaultValue = false)
    {
        return array_key_exists($key, $array) ? $array[$key] : $defaultValue;
    }

    public static function isDate($date, $format = 'Y-m-d')
    {
        $dateTime = \DateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) == $date;
    }
}
