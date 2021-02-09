<?php declare(strict_types = 1);

namespace WebServCo\Framework\Libraries;

final class MysqlPdoDatabase extends \WebServCo\Framework\Database\AbstractPdoDatabase implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{

    use \WebServCo\Framework\Traits\MysqlDatabaseTrait;

    protected function getDataSourceName(string $host, string $port, string $dbname): string
    {
        return \sprintf('%s:host=%s;port=%s;dbname=%s;charset=%s', 'mysql', $host, $port, $dbname, 'utf8mb4');
    }
}
