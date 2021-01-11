<?php
namespace WebServCo\Framework\Interfaces;

interface DatabaseInterface
{
    public function affectedRows() : int;

    /**
    * @param string $query
    * @param array<int, float|int|string> $params
    * @param int $columnNumber
    * @return bool|int|null|string
    */
    public function getColumn(string $query, array $params = [], int $columnNumber = 0);

    /**
    * @param string $query
    * @param array<int, float|int|string> $params
    * @return array<string, float|int|string>
    */
    public function getRow(string $query, array $params = []) : array;

    /**
    * @param string $query
    * @param array<int, float|int|string> $params
    * @return array<string, float|int|string>
    */
    public function getRows(string $query, array $params = []) : array;

    public function escape(string $string) : string;

    public function escapeIdentifier(string $string) : string;

    public function escapeTableName(string $string) : string;

    public function lastInsertId() : string;

    public function numRows() : int;

    /**
    * @param string $query
    * @param array<int, float|int|string> $params
    * @return \PDOStatement
    */
    public function query(string $query, array $params = []);

    /**
    * @param array<int,array<int,mixed>> $queries
    * @return bool
    */
    public function transaction(array $queries) : bool;

    /**
    * @param string $table
    * @param string $field
    * @param float|int|string $value
    * @return bool
    */
    public function valueExists(string $table, string $field, $value) : bool;
}
