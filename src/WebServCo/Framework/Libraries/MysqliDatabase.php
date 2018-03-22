<?php
namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\Settings as S;
use WebServCo\Framework\Exceptions\DatabaseException;

final class MysqliDatabase extends \WebServCo\Framework\AbstractDatabase implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    use \WebServCo\Framework\Traits\DatabaseTrait;
    use \WebServCo\Framework\Traits\MysqlDatabaseTrait;

    protected $mysqliResult;

    public function __construct($settings = [])
    {
        parent::__construct($settings);

        $driver = new \mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

        try {
            $this->db = new \mysqli(
                $this->setting(sprintf('connection%shost', S::DIVIDER), '127.0.0.1'),
                $this->setting(sprintf('connection%susername', S::DIVIDER), 'root'),
                $this->setting(sprintf('connection%spasswd', S::DIVIDER), ''),
                $this->setting(sprintf('connection%sdbname', S::DIVIDER), 'test'),
                $this->setting(sprintf('connection%sport', S::DIVIDER), 3306)
            );
            $this->db->set_charset('utf8mb4');
        } catch (\Exception $e) { // mysqli_sql_exception/RuntimeException/Exception
            throw new DatabaseException($e->getMessage());
        }
    }

    public function escape($string)
    {
        return $this->db->real_escape_string($string);
    }

    public function query($query, $params = [])
    {
        if (empty($query)) {
            throw new DatabaseException('No query specified');
        }

        try {
            /**
             * For simplicity use statements even for simple queries.
             */
            $this->stmt = $this->db->prepare($query);
            $this->bindParams($params);
            $this->stmt->execute();
            $this->setLastInsertId();
            return $this->stmt;
        } catch (\Exception $e) { // mysqli_sql_exception/RuntimeException/Exception
            throw new DatabaseException($e->getMessage());
        }
    }

    public function transaction($queries)
    {
        try {
            $this->db->autocommit(false);
            foreach ($queries as $item) {
                if (!isset($item[0])) {
                    throw new DatabaseException('No query specified');
                }
                $params = isset($item[1]) ? $item[1] : [];
                $this->query($item[0], $params);
            }
            $this->db->commit();
            return true;
        } catch (\Exception $e) { // mysqli_sql_exception/RuntimeException/Exception
            $this->db->rollback();
            throw new DatabaseException($e->getMessage());
        }
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
        throw new DatabaseException('Method not implemented.');
    }

    public function affectedRows()
    {
        if (!($this->stmt instanceof \mysqli_stmt)) {
            throw new DatabaseException('No Statement object available.');
        }
        return $this->stmt->affected_rows;
    }

    public function getRows($query, $params = [])
    {
        $this->query($query, $params);
        $this->mysqliResult = $this->stmt->get_result();
        $this->rows = $this->mysqliResult->fetch_all(MYSQLI_ASSOC);
        return $this->rows;
    }

    public function getRow($query, $params = [])
    {
        $this->query($query, $params);
        $this->mysqliResult = $this->stmt->get_result();
        return $this->mysqliResult->fetch_assoc();
    }

    public function getColumn($query, $params = [], $columnNumber = 0)
    {
        $this->query($query, $params);
        $this->mysqliResult = $this->stmt->get_result();
        $row = $this->mysqliResult->fetch_array(MYSQLI_NUM);
        return array_key_exists($columnNumber, $row) ? $row[$columnNumber] : false;
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

        return call_user_func_array([$this->stmt, 'bind_param'], $args);
    }

    protected function getDataType($variable)
    {
        $type = gettype($variable);

        switch ($type) {
            case 'integer':
                return 'i';
                break;
            case 'double':
                return 'd';
                break;
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
                break;
        }
    }

    protected function setLastInsertId()
    {
        $this->lastInsertId = $this->db->insert_id;
    }
}
