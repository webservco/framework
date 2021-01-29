<?php
namespace WebServCo\Framework\Utils;

final class Dates
{
    public static function format(string $date, string $format = 'Y-m-d'): string
    {
        $dateTime = \DateTime::createFromFormat($format, $date);
        if (false == $dateTime) {
            throw new \InvalidArgumentException('Invalid date.');
        }
        return $dateTime->format($format);
    }

    public static function isDate(string $date, string $format = 'Y-m-d'): bool
    {
        $formattedDate = self::format($date, $format);
        
        return $formattedDate == $date;
    }
}
