<?php
namespace WebServCo\Framework\Libraries;

final class PgsqlPdoDatabase extends \WebServCo\Framework\AbstractPdoDatabase implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    use \WebServCo\Framework\Traits\PgsqlDatabaseTrait;

    protected function getDataSourceName($host, $port, $dbname)
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
