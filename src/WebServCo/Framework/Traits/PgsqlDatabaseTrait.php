<?php
namespace WebServCo\Framework\Traits;

trait PgsqlDatabaseTrait
{
    public function escapeIdentifier($string)
    {
        return '"' . str_replace('"', '""', $string) . '"';
    }

    public function escapeTableName($string)
    {
        // @TODO Fix.
        throw new \WebServCo\Framework\Exceptions\NotImplementedException(
            sprintf('Method not implemented. String: %s.', $string)
        );
    }
}
