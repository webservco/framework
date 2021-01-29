<?php
namespace WebServCo\Framework\Libraries;

final class PgsqlPdoDatabase extends \WebServCo\Framework\Database\AbstractPdoDatabase implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    use \WebServCo\Framework\Traits\PgsqlDatabaseTrait;

    protected function getDataSourceName(string $host, string $port, string $dbname): string
    {
        return sprintf(
            '%s:host=%s;port=%s;dbname=%s',
            'pgsql',
            $host,
            $port,
            $dbname
        );
    }
}
