<?php

namespace WebServCo\Framework\Libraries;

use RuntimeException;
use WebServCo\Framework\Settings;
use WebServCo\Framework\Exceptions\DatabaseException;

final class MysqliDatabase extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    protected $db;
    protected $stmt;
    protected $rows;

    use \WebServCo\Framework\Traits\DatabaseTrait;
    use \WebServCo\Framework\Traits\DatabaseAddQueryTrait;
    use \WebServCo\Framework\Traits\MysqlDatabaseTrait;

    protected $mysqliResult;

    public function __construct($settings = [])
    {
        parent::__construct($settings);

        $driver = new \mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

        try {
            $this->db = new \mysqli(
                $this->setting(sprintf('connection%shost', Settings::DIVIDER), '127.0.0.1'),
                $this->setting(sprintf('connection%susername', Settings::DIVIDER), 'root'),
                $this->setting(sprintf('connection%spasswd', Settings::DIVIDER), ''),
                $this->setting(sprintf('connection%sdbname', Settings::DIVIDER), 'test'),
                $this->setting(sprintf('connection%sport', Settings::DIVIDER), 3306)
            );
            $this->db->set_charset('utf8mb4');
        } catch (\Exception $e) { // \mysqli_sql_exception, \RuntimeException
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    public function affectedRows()
    {
        if (!($this->stmt instanceof \mysqli_stmt)) {
            throw new DatabaseException('No Statement object available.');
        }
        return $this->stmt->affected_rows;
    }

    public function escape($string)
    {
        return $this->db->real_escape_string($string);
    }

    public function getColumn($query, $params = [], $columnNumber = 0)
    {
        $this->query($query, $params);
        $this->mysqliResult = $this->stmt->get_result();
        $row = $this->mysqliResult->fetch_array(MYSQLI_NUM);
        return array_key_exists($columnNumber, $row) ? $row[$columnNumber] : false;
    }

    public function getPdo()
    {
        throw new RuntimeException('Functionality not implemented.');
    }

    public function getRow($query, $params = [])
    {
        $this->query($query, $params);
        $this->mysqliResult = $this->stmt->get_result();
        return $this->mysqliResult->fetch_assoc();
    }

    public function getRows($query, $params = [])
    {
        $this->query($query, $params);
        $this->mysqliResult = $this->stmt->get_result();
        $this->rows = $this->mysqliResult->fetch_all(MYSQLI_ASSOC);
        return $this->rows;
    }

    /**
     * Get last inserted Id.
     *
     * https://dev.mysql.com/doc/refman/5.5/en/information-functions.html#function_last-insert-id
     * If you insert multiple rows using a single INSERT statement,
     * LAST_INSERT_ID() returns the value generated for the first inserted row only.
     * The reason for this is to make it possible to reproduce easily the same
     * INSERT statement against some other server.
     */
    public function lastInsertId()
    {
        return (int) $this->db->insert_id;
    }

    public function numRows()
    {
        /**
         * @TODO Fix.
         * "$this->stmt->num_rows" will be 0 because we can't use
         * "$this->stmt->store_result();"
         * We could count "$this->mysqliResult" but that would mean
         * the method will only work if getRow*() was called before.
         */
        throw new \WebServCo\Framework\Exceptions\NotImplementedException('Method not implemented.');
    }

    public function query($query, $params = [])
    {
        if (empty($query)) {
            throw new DatabaseException('No query specified.');
        }

        try {
            /**
             * For simplicity use statements even for simple queries.
             */
            $this->stmt = $this->db->prepare($query);
            $this->bindParams($params);
            $this->stmt->execute();
            return $this->stmt;
        } catch (\Exception $e) { // \mysqli_sql_exception, \RuntimeException
            throw new DatabaseException($e->getMessage());
        }
    }

    public function transaction($queries)
    {
        try {
            $this->db->autocommit(false);
            foreach ($queries as $item) {
                if (!isset($item[0])) {
                    throw new DatabaseException('No query specified.');
                }
                $params = isset($item[1]) ? $item[1] : [];
                $this->query($item[0], $params);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) { // \mysqli_sql_exception, \RuntimeException
            $this->db->rollback();
            throw new DatabaseException($e->getMessage());
        }
    }

    protected function bindParams($params = [])
    {
        if (empty($params)) {
            return false;
        }

        $types = [];
        $values = [];
        foreach ($params as $item) {
            if (is_array($item)) {
                foreach ($item as $value) {
                    $types[] = $this->getDataType($value);
                    $values[] = $value;
                }
            } else {
                $types[] = $this->getDataType($item);
                $values[] = $item;
            }
        }

        $typeString = implode('', $types);
        $args = [
            0 => &$typeString,
        ];
        foreach ($values as &$v) {
            $args[] = &$v;
        }
        $callable = [$this->stmt, 'bind_param'];
        if (!is_callable($callable)) {
            throw new DatabaseException('Method not found.');
        }
        return call_user_func_array($callable, $args);
    }

    protected function getDataType($variable)
    {
        $type = gettype($variable);

        switch ($type) {
            case 'integer':
                return 'i';
            case 'double':
                return 'd';
            case 'string':
            case 'boolean':
            case 'NULL':
            case 'array':
            case 'object':
            case 'resource':
            case 'resource (closed)':
            case 'unknown type':
            default:
                return 's';
        }
    }
}
