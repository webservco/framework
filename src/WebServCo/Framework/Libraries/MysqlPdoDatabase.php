<?php
namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\Exceptions\DatabaseException;

final class MysqlPdoDatabase extends \WebServCo\Framework\Database\AbstractPdoDatabase implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    use \WebServCo\Framework\Traits\MysqlDatabaseTrait;

    protected function getDataSourceName($host, $port, $dbname)
    {
        return sprintf(
            '%s:host=%s;port=%s;dbname=%s;charset=%s',
            'mysql',
            $host,
            $port,
            $dbname,
            'utf8mb4'
        );
    }
}
