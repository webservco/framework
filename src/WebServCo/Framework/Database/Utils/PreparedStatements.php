<?php

declare(strict_types=1);

namespace WebServCo\Framework\Database\Utils;

final class PreparedStatements
{
    /**
    * @param array<int,float|int|string> $data
    */
    public static function generatePlaceholdersString(array $data = []): string
    {
        /**
        * Pre PHP 7.4 Anonymous function: `static function () { return '?';}`
        */
        return \implode(', ', \array_map(static fn () => '?', $data));
    }
}
