<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\Database\AbstractPdoDatabase;
use WebServCo\Framework\Interfaces\DatabaseInterface;
use WebServCo\Framework\Traits\PgsqlDatabaseTrait;

use function sprintf;

final class PgsqlPdoDatabase extends AbstractPdoDatabase implements
    DatabaseInterface
{
    use PgsqlDatabaseTrait;

    protected function getDataSourceName(string $host, int $port, string $dbname): string
    {
        return sprintf('%s:host=%s;port=%s;dbname=%s', 'pgsql', $host, $port, $dbname);
    }
}
