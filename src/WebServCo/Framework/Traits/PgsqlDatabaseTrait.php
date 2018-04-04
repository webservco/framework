<?php
namespace WebServCo\Framework\Traits;

trait PgsqlDatabaseTrait
{
    public function escapeIdentifier($string)
    {
        return '"' . str_replace('"', '""', $string) . '"';
    }
}
