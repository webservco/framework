<?php
namespace WebServCo\Framework\Interfaces;

interface DatabaseInterface
{
    public function getPdo();

    public function escape($string);

    public function query($query, $values = []);

    public function transaction($queries);

    public function numRows();

    public function affectedRows();

    public function getRows($query, $params = []);

    public function getRow($query, $params = []);

    public function getColumn($query, $params = [], $columnNumber = 0);

    public function lastInsertId();

    public function escapeIdentifier($string);

    public function escapeTableName($string);
}
