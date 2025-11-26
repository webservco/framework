<?php

declare(strict_types=1);

namespace WebServCo\Framework\Database\Utils;

use function count;
use function implode;

final class PreparedStatements
{
    /**
    * @param array<int,float|int|string> $array
    */
    public static function generatePlaceholdersString(array $array = []): string
    {
        $data = [];
        for ($i = 0; $i < count($array); $i += 1) {
            $data[] = '?';
        }

        return implode(',', $data);
    }
}
