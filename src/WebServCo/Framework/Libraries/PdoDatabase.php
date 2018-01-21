<?php
namespace WebServCo\Framework\Libraries;

final class PdoDatabase extends \WebServCo\Framework\AbstractDatabase implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    use \WebServCo\Framework\Traits\DatabaseTrait;
    
    public function __construct($config)
    {
        parent::__construct($config);
        
        try {
            $this->db = new \PDO(
                $this->setting('driver', 'mysql') .
                ':host=' . $this->setting('connection/host', '127.0.0.1') .
                ';dbname=' . $this->setting('connection/dbname', 'test') .
                ';port=' . $this->setting('connection/port', 3306) .
                ';charset=utf8mb4',
                $this->setting('connection/username', 'root'),
                $this->setting('connection/passwd', ''),
                [
                    \PDO::ATTR_EMULATE_PREPARES => false,
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_PERSISTENT => false
                ]
            );
        } catch (\PDOException $e) {
            throw new \ErrorException($e->getMessage());
        }
    }
    
    public function escape($string)
    {
        return $this->db->quote($string);
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
        //$this->stmt->debugDumpParams(); exit; //XXX
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
            $this->db->beginTransaction();
            foreach ($data as $item) {
                if (!isset($item[0])) {
                    throw new \ErrorException('No query specified');
                }
                $params = isset($item[1]) ? $item[1] : [];
                $this->executeQuery($item[0], $params);
            }
            $this->db->commit();
            return true;
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new \ErrorException($e->getMessage());
        }
    }
    
    public function numRows()
    {
        if (!is_object($this->stmt)) {
            return false;
        }
        $rows = $this->rows ?: $this->stmt->fetchAll(PDO::FETCH_ASSOC);
        return count($rows);
    }
    
    public function affectedRows()
    {
        return $this->stmt->rowCount();
    }
        
    protected function bindParams($data)
    {
        if (empty($data)) {
            return false;
        }
        
        $i = 1;
        foreach ($data as $item) {
            if (is_array($item)) {
                foreach ($item as $v) {
                    $this->stmt->bindValue($i, $v, $this->getDataType($v));
                    $i++;
                }
            } else {
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
                return PDO::PARAM_NULL;
                break;
            case 'boolean':
                return \PDO::PARAM_BOOL;
                break;
            case 'integer':
                return \PDO::PARAM_INT;
                break;
            case 'string':
            case 'double':
            case 'array':
            case 'object':
            case 'resource':
            case 'resource (closed)':
            case 'unknown type':
                return \PDO::PARAM_STR;
                break;
        }
    }
    
    protected function setLastInsertId()
    {
        $this->lastInsertId = $this->db->lastInsertId();
    }
}
