<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Database\QueryType;

trait DatabaseTrait
{
    abstract public function escapeIdentifier(string $string): string;

    abstract public function escapeTableName(string $string): string;

    /**
    * @param string $queryType
    * @param string $tableName
    * @param array<string, float|int|string> $addData
    * @param array<string, float|int|string> $updateData
    * @return string
    */
    abstract protected function generateAddQuery(
        string $queryType,
        string $tableName,
        array $addData = [],
        array $updateData = []
    ): string;

    /**
    * @param string $query
    * @param array<int, float|int|string> $params
    * @param int $columnNumber
    * @return null|string
    */
    abstract public function getColumn(string $query, array $params = [], int $columnNumber = 0)  : ?string;

    /**
    * @param string $query
    * @param array<int, float|int|string> $params
    * @return \PDOStatement
    */
    abstract public function query(string $query, array $params = []): \PDOStatement;

    /**
    * @param string $tableName
    * @param array<string,float|int|string> $addData
    * @param array<string,float|int|string> $updateData
    * @return \PDOStatement
    */
    final public function insert(string $tableName, array $addData = [], array $updateData = []): \PDOStatement
    {
        return $this->add(QueryType::INSERT, $tableName, $addData, $updateData);
    }

    /**
    * @param string $tableName
    * @param array<string, float|int|string> $data
    * @return \PDOStatement
    */
    final public function insertIgnore(string $tableName, array $data = []): \PDOStatement
    {
        return $this->add(QueryType::INSERT_IGNORE, $tableName, $data);
    }

    /**
    * @param string $tableName
    * @param array<string, float|int|string> $data
    * @return \PDOStatement
    */
    final public function replace(string $tableName, array $data = []): \PDOStatement
    {
        return $this->add(QueryType::REPLACE, $tableName, $data);
    }

    /**
    * @param string $queryType
    * @param string $tableName
    * @param array<string, float|int|string> $addData
    * @param array<string, float|int|string> $updateData
    * @return \PDOStatement
    */
    final protected function add(
        string $queryType,
        string $tableName,
        array $addData = [],
        array $updateData = []
    ): \PDOStatement {
        if (empty($tableName)) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException('No data specified.');
        }

        $query = $this->generateAddQuery($queryType, $tableName, $addData, $updateData);

        $queryData = [];
        foreach ($addData as $item) {
            $queryData[] = $item;
        }
        foreach ($updateData as $item) {
            $queryData[] = $item;
        }

        return $this->query($query, $queryData);
    }

    /**
    * @param string $table
    * @param string $field
    * @param float|int|string $value
    * @return bool
    */
    final public function valueExists(string $table, string $field, $value): bool
    {
        return (bool) $this->getColumn(
            sprintf(
                "SELECT 1 FROM %s WHERE %s = ? LIMIT 1",
                $this->escapeTableName($table),
                $this->escapeIdentifier($field)
            ),
            [$value]
        );
    }

    final public function tableExists(string $table): bool
    {
        $name = $this->escapeTableName($table);

        try {
            $this->query(sprintf('SELECT 1 FROM %s LIMIT 1', $name));
            return true;
        } catch (\WebServCo\Framework\Exceptions\DatabaseException $e) {
            return false;
        }
    }
}
