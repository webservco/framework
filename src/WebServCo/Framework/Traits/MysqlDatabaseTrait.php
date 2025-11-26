<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use function explode;
use function sprintf;
use function str_replace;

trait MysqlDatabaseTrait
{
    public function escapeIdentifier(string $string): string
    {
        return '`' . str_replace('`', '``', $string) . '`';
    }

    public function escapeTableName(string $string): string
    {
        $parts = explode('.', $string);
        if (!empty($parts[1])) {
            return sprintf(
                '%s.%s',
                $this->escapeIdentifier($parts[0]),
                $this->escapeIdentifier($parts[1]),
            );
        }

        return $this->escapeIdentifier($parts[0]);
    }
}
