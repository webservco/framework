<?php
namespace WebServCo\Framework\Libraries;

final class MysqliDatabase extends \WebServCo\Framework\AbstractDatabase implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    use \WebServCo\Framework\Traits\DatabaseTrait;
    
    public function __construct($config)
    {
        parent::__construct($config);
        
        $driver = new \mysqli_driver();
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;
        
        try {
            $this->db = new \mysqli(
                $this->setting('connection/host', '127.0.0.1'),
                $this->setting('connection/username', 'root'),
                $this->setting('connection/passwd', ''),
                $this->setting('connection/dbname', 'test'),
                $this->setting('connection/port', 3306)
            );
            $this->db->set_charset('utf8mb4');
        } catch (\mysqli_sql_exception $e) {
            throw new \ErrorException($e->getMessage());
        }
    }
    
    public function escape($string)
    {
        return $this->db->real_escape_string($string);
    }
    
    public function executeQuery($query, $params = [])
    {
        if (empty($query)) {
            throw new \ErrorException('No query specified');
        }
        $this->stmt = $this->db->prepare($query);
        if (!empty($params)) {
            $this->bindParams($params);
        }
        $result = $this->stmt->execute();
        if (!$result) {
            return false;
        }
        $this->setLastInsertId();
        return true;
    }
    
    public function executeTransaction($data)
    {
        try {
            $this->db->autocommit(false);
            foreach ($data as $item) {
                if (!isset($item[0])) {
                    throw new \ErrorException('No query specified');
                }
                $params = isset($item[1]) ? $item[1] : [];
                $this->executeQuery($item[0], $params);
            }
            $this->db->commit();
            return true;
        } catch (\mysqli_sql_exception $e) {
            $this->db->rollback();
            throw new \ErrorException($e->getMessage());
        }
    }
    
    public function numRows()
    {
        return $this->stmt->num_rows;
    }
    
    public function affectedRows()
    {
        return $this->stmt->affected_rows;
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
        
        $typeString = implode(null, $types);
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
                return 's';
                break;
        }
    }
    
    protected function setLastInsertId()
    {
        $this->lastInsertId = $this->db->insert_id;
    }
}
