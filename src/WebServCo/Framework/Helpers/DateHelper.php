<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Exceptions\DateTimeException;

class DateHelper
{
    /**
    * Format a date.
    *
    * Warning: Because of how \strtotime works, an invalid date will be converted to the next valid value.
    * If you need to validate a date, do not format it beforehand.
    * Use either format or validaton, but not both.
    */
    public static function format(string $date, string $format = 'Y-m-d'): string
    {
        return \date($format, (int) \strtotime($date));
    }

    /**
    * Validate a date according to format.
    */
    public static function validate(string $date, string $format = 'Y-m-d'): bool
    {
        // "!"
        // "Resets all fields (year, month, day, hour, minute, second, fraction and timezone information)
        // to zero-like values ( 0 for hour, minute, second and fraction, 1 for month and day, 1970 for year
        // and UTC for timezone information)"
        // "Without !, all fields will be set to the current date and time."
        $dateTime = \DateTime::createFromFormat(\sprintf('!%s', $format), $date);
        if (false === $dateTime) {
            throw new DateTimeException('Invalid date or format.');
        }

        // \DateTime::createFromFormat will change the original data if not valid.
        if ($dateTime->format($format) !== $date) {
            throw new DateTimeException('Invalid date.');
        }

        return true;
    }
}
