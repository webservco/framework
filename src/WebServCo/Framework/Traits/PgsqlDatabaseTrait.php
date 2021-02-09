<?php declare(strict_types = 1);

namespace WebServCo\Framework\Traits;

trait PgsqlDatabaseTrait
{

    public function escapeIdentifier(string $string): string
    {
        return '"' . \str_replace('"', '""', $string) . '"';
    }

    public function escapeTableName(string $string): string
    {
        // @TODO Fix.
        throw new \WebServCo\Framework\Exceptions\NotImplementedException(
            \sprintf('Method not implemented. String: %s.', $string)
        );
    }
}
