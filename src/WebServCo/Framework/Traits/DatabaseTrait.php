<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use PDOStatement;
use WebServCo\Framework\Database\QueryType;
use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Exceptions\DatabaseException;

use function sprintf;

trait DatabaseTrait
{
    /**
    * @param array<mixed> $addData
    * @param array<mixed> $updateData
    *
    * Returns lastInsertId
    */
    final public function insert(string $tableName, array $addData = [], array $updateData = []): int
    {
        return $this->add(QueryType::INSERT, $tableName, $addData, $updateData);
    }

    /**
    * @param array<mixed> $data
    *
    * Returns lastInsertId
    */
    final public function insertIgnore(string $tableName, array $data = []): int
    {
        return $this->add(QueryType::INSERT_IGNORE, $tableName, $data);
    }

    /**
    * @param array<mixed> $data
    *
    * Returns lastInsertId
    */
    final public function replace(string $tableName, array $data = []): int
    {
        return $this->add(QueryType::REPLACE, $tableName, $data);
    }

    final public function valueExists(string $table, string $field, float|int|string $value): bool
    {
        return (bool) $this->getColumn(
            sprintf(
                "SELECT 1 FROM %s WHERE %s = ? LIMIT 1",
                $this->escapeTableName($table),
                $this->escapeIdentifier($field),
            ),
            [$value],
        );
    }

    final public function tableExists(string $table): bool
    {
        $name = $this->escapeTableName($table);

        try {
            $this->query(sprintf('SELECT 1 FROM %s LIMIT 1', $name));

            return true;
        } catch (DatabaseException) {
            return false;
        }
    }

    abstract public function escapeIdentifier(string $string): string;

    abstract public function escapeTableName(string $string): string;

    /**
    * @param array<int,float|int|string> $params
    */
    abstract public function getColumn(
        string $query,
        array $params = [],
        int $columnNumber = 0,
    ): string|float|int|bool|null;

    /**
    * @param array<int,float|int|string|null> $params
    */
    abstract public function query(string $query, array $params = []): PDOStatement;

    /**
    * @param array<int,array<int,mixed>> $queries
    */
    abstract public function transaction(array $queries): int;

    /**
    * @param array<mixed> $addData
    * @param array<mixed> $updateData
    *
    * Returns lastInsertId
    */
    final protected function add(string $queryType, string $tableName, array $addData = [], array $updateData = []): int
    {
        if (!$tableName) {
            throw new ApplicationException('No data specified.');
        }

        $query = $this->generateAddQuery($queryType, $tableName, $addData, $updateData);

        $queryData = [];
        foreach ($addData as $item) {
            $queryData[] = $item;
        }
        foreach ($updateData as $item) {
            $queryData[] = $item;
        }

        return $this->transaction(
            [
                // item
                [$query, $queryData],
            // array
            ],
        );
    }

    /**
    * @param array<string,float|int|string|null> $addData
    * @param array<string,float|int|string|null> $updateData
    */
    abstract protected function generateAddQuery(
        string $queryType,
        string $tableName,
        array $addData = [],
        array $updateData = [],
    ): string;
}
