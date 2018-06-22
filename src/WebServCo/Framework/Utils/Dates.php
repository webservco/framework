<?php
namespace WebServCo\Framework\Utils;

final class Dates
{
    public static function isDate($date, $format = 'Y-m-d')
    {
        $dateTime = \DateTime::createFromFormat($format, $date);
        return $dateTime && $dateTime->format($format) == $date;
    }
}
