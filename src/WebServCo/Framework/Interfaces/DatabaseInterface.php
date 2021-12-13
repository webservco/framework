<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface DatabaseInterface
{
    public function affectedRows(): int;

    /**
    * @param array<int,float|int|string> $params
    * @return bool|int|string|null
    */
    public function getColumn(string $query, array $params = [], int $columnNumber = 0);

    /**
    * @param array<int,float|int|string> $params
    * @return array<string,float|int|string>
    */
    public function getRow(string $query, array $params = []): array;

    /**
    * @param array<int,float|int|string> $params
    * @return array<int,array<string,float|int|string>>
    */
    public function getRows(string $query, array $params = []): array;

    public function escape(string $string): string;

    public function escapeIdentifier(string $string): string;

    public function escapeTableName(string $string): string;

    /**
    * @param array<mixed> $addData
    * @param array<mixed> $updateData
    *
    * Returns lastInsertId
    */
    public function insert(string $tableName, array $addData = [], array $updateData = []): int;

    /**
    * @param array<mixed> $data
    *
    * Returns lastInsertId
    */
    public function insertIgnore(string $tableName, array $data = []): int;

    public function lastInsertId(): int;

    public function numRows(): int;

    /**
    * @param array<int,float|int|string|null> $params
    */
    public function query(string $query, array $params = []): \PDOStatement;

    /**
    * @param array<mixed> $data
    */
    public function replace(string $tableName, array $data = []): int;

    public function tableExists(string $table): bool;

    /**
    * @param array<int,array<int,mixed>> $queries
    *
    * Returns lastInsertId.
    */
    public function transaction(array $queries): int;

    /**
    * @param float|int|string $value
    */
    public function valueExists(string $table, string $field, $value): bool;
}
