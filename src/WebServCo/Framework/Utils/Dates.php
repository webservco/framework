<?php
namespace WebServCo\Framework\Utils;

final class Dates
{
    public static function format($date, $format = 'Y-m-d')
    {
        $dateTime = \DateTime::createFromFormat($format, $date);
        if (false == $dateTime) {
            return false;
        }
        return $dateTime->format($format);
    }

    public static function isDate($date, $format = 'Y-m-d')
    {
        $formattedDate = self::format($date, $format);
        return $formattedDate == $date;
    }
}
