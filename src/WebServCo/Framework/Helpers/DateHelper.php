<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Exceptions\DateTimeException;

class DateHelper
{
    /**
    * Format a date, with validation.
    */
    public static function format(string $date, string $format = 'Y-m-d'): string
    {
        $dateTime = \DateTime::createFromFormat($format, $date);
        if (false === $dateTime) {
            throw new DateTimeException('Invalid date or format.');
        }
        return $dateTime->format($format);
    }

    /**
    * Validate an already formatted date.
    */
    public static function validate(string $date, string $format = 'Y-m-d'): bool
    {
        $formattedDate = self::format($date, $format);

        if ($formattedDate !== $date) {
            throw new DateTimeException('Invalid date or format.');
        }

        return true;
    }
}
