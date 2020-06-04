<?php
namespace WebServCo\Framework\Database;

use WebServCo\Framework\Exceptions\DatabaseException;

abstract class AbstractPdoDatabase extends \WebServCo\Framework\AbstractLibrary
{
    protected $db;
    protected $stmt;
    protected $rows;

    use \WebServCo\Framework\Traits\DatabaseTrait;
    use \WebServCo\Framework\Traits\DatabaseAddQueryTrait;

    abstract protected function getDataSourceName($host, $port, $dbname);

    public function __construct($settings = [])
    {
        parent::__construct($settings);

        try {
            $dsn = $this->getDataSourceName(
                $this->setting('connection/host', '127.0.0.1'),
                $this->setting('connection/port', null),
                $this->setting('connection/dbname', 'test')
            );
            $this->db = new \PDO(
                $dsn,
                $this->setting('connection/username', 'root'),
                $this->setting('connection/passwd', ''),
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_PERSISTENT => false
                ]
            );
        } catch (\Exception $e) { // PDOException/RuntimeException/Exception
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    public function affectedRows()
    {
        if (!($this->stmt instanceof \PDOStatement)) {
            throw new DatabaseException('No Statement object available.');
        }
        return $this->stmt->rowCount();
    }

    public function escape($string)
    {
        return $this->db->quote($string);
    }

    public function getColumn($query, $params = [], $columnNumber = 0)
    {
        $this->query($query, $params);
        return $this->stmt->fetchColumn($columnNumber);
    }

    public function getRow($query, $params = [])
    {
        $this->query($query, $params);
        return $this->stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function getRows($query, $params = [])
    {
        $this->query($query, $params);
        $this->rows = $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
        return $this->rows;
    }

    /*
    * Get last inserted Id.
    *
    * https://dev.mysql.com/doc/refman/5.5/en/information-functions.html#function_last-insert-id
    * If you insert multiple rows using a single INSERT statement,
    * LAST_INSERT_ID() returns the value generated for the first inserted row only.
    * The reason for this is to make it possible to reproduce easily the same
    * INSERT statement against some other server.
    *
    * PDO:
    * Returns the ID of the last inserted row, or the last value from a sequence object,
    * depending on the underlying driver.
    * For example, PDO_PGSQL requires you to specify the name of a sequence object for the name parameter.
    */
    public function lastInsertId($name = null)
    {
        return $this->db->lastInsertId($name);
    }

    public function numRows()
    {
        if (!($this->stmt instanceof \PDOStatement)) {
            throw new DatabaseException('No Statement object available.');
        }
        if ('mysql' == $this->setting('driver')) {
            return $this->stmt->rowCount();
        }
        $rows = $this->rows ?: $this->stmt->fetchAll(\PDO::FETCH_ASSOC);
        return count($rows);
    }

    public function query($query, $params = [])
    {
        if (empty($query)) {
            throw new DatabaseException('No query specified.');
        }

        try {
            if (!empty($params)) {
                $this->stmt = $this->db->prepare($query);
                $this->bindParams($params);
                $this->stmt->execute();
            } else {
                $this->stmt = $this->db->query($query);
            }
            return $this->stmt;
        } catch (\PDOException $e) {
            throw new DatabaseException($e->getMessage(), $e);
        } catch (\RuntimeException $e) {
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    public function transaction($queries)
    {
        try {
            $this->db->beginTransaction();
            foreach ($queries as $item) {
                if (!isset($item[0])) {
                    throw new DatabaseException('No query specified.');
                }
                $params = isset($item[1]) ? $item[1] : [];
                $this->query($item[0], $params);
            }
            $this->db->commit();
            return true;
        // \WebServCo\Framework\Exceptions\DatabaseException
        // PDOException/RuntimeException/Exception
        } catch (\Exception $e) {
            $this->db->rollBack();
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    protected function bindParams($data)
    {
        if (empty($data)) {
            return false;
        }

        if (!is_array($data)) {
            throw new DatabaseException('"Parameters" is not an array.');
        }

        $i = 1;
        foreach ($data as $item) {
            if (is_array($item)) {
                foreach ($item as $v) {
                    $this->validateParam($v);
                    $this->stmt->bindValue($i, $v, $this->getDataType($v));
                    $i++;
                }
            } else {
                $this->validateParam($item);
                $this->stmt->bindValue($i, $item, $this->getDataType($item));
                $i++;
            }
        }
        return true;
    }

    protected function getDataType($variable)
    {
        $type = gettype($variable);

        switch ($type) {
            case 'NULL':
                return \PDO::PARAM_NULL;
            case 'integer':
                return \PDO::PARAM_INT;
            case 'boolean':
                // causes data not to be inserted
                //return \PDO::PARAM_BOOL;
            case 'string':
            case 'double':
            case 'array':
            case 'object':
            case 'resource':
            case 'resource (closed)':
            case 'unknown type':
            default:
                return \PDO::PARAM_STR;
        }
    }

    protected function validateParam($param)
    {
        if (is_array($param)) {
            throw new DatabaseException('Parameter is an array.');
        }
        return true;
    }
}
