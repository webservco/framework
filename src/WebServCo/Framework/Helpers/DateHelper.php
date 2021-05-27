<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

class DateHelper
{
    /**
    * Format a date, with validation.
    */
    public static function format(string $date, string $format = 'Y-m-d'): string
    {
        $dateTime = \DateTime::createFromFormat($format, $date);
        if (false === $dateTime) {
            throw new \WebServCo\Framework\Exceptions\DateTimeException('Invalid date or format.');
        }
        return $dateTime->format($format);
    }
}
