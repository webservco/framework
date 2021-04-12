<?php

declare(strict_types=1);

namespace WebServCo\Framework\Utils;

final class Numbers
{
    /**
    * @param mixed $number
    */
    public static function isEmpty($number): bool
    {
        $number = \floatval($number);

        return (bool) $number;
    }
}
