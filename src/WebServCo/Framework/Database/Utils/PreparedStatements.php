<?php declare(strict_types = 1);

namespace WebServCo\Framework\Database\Utils;

final class PreparedStatements
{
    /**
    * @param array<int, float|int|string> $data
    * @return string
    */
    public static function generatePlaceholdersString(array $data = []): string
    {
        return implode(', ', array_map(function () {
            return '?';
        }, $data));
    }
}
