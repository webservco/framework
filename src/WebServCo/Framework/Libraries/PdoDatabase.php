<?php
namespace WebServCo\Framework\Libraries;

final class PdoDatabase extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    const QUERY_TYPE_INSERT = 'INSERT';
    const QUERY_TYPE_INSERT_IGNORE = 'INSERT IGNORE';
    const QUERY_TYPE_REPLACE = 'REPLACE';
    
    protected $db;
    protected $stmt;
    
    public function __construct($config)
    {
        parent::__construct($config);
        
        $this->db = new \PDO(
            $this->setting('driver', 'mysql') .
            ':host=' . $this->setting('connection/host', '127.0.0.1') .
            ';dbname=' . $this->setting('connection/dbname', 'test') .
            ';charset=utf8mb4',
            $this->setting('connection/username', 'root'),
            $this->setting('connection/passwd', ''),
            [
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_PERSISTENT => false
            ]
        );
    }
    
    public function escape($string)
    {
        return $this->db->quote($string);
    }
    
    public function getDataType($variable)
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
    
    protected function getKeysValues($data = [], $multiDimensional = false)
    {
        if ($multiDimensional) {
            $keys = array_keys(call_user_func_array('array_merge', $data));
            // fill any missing keys with empty data
            $key_pair = array_combine($keys, array_fill(0, count($keys), null));
            $data = array_map(function ($e) use ($key_pair) {
                return array_merge($key_pair, $e);
            }, $data);
        } else {
            $keys = array_keys($data);
        }
        
        return [$keys, $data];
    }
    
    protected function generateAddQuery($queryType, $tableName, $keys, $data, $multiDimensional = false)
    {
        switch ($queryType) {
            case self::QUERY_TYPE_REPLACE:
                $query = self::QUERY_TYPE_REPLACE . ' INTO';
                break;
            case self::QUERY_TYPE_INSERT_IGNORE:
                $query = self::QUERY_TYPE_INSERT_IGNORE . ' INTO';
                break;
            case self::QUERY_TYPE_INSERT:
            default:
                $query = self::QUERY_TYPE_INSERT . ' INTO';
                break;
        }
        
        $query .= " `{$tableName}` (" .
        implode(', ', array_map(function ($v) {
            return "`{$v}`";
        }, $keys)) .
        ') VALUES';
        if ($multiDimensional) {
            $valuesStrings = [];
            foreach ($data as $item) {
                $valuesStrings[] = $this->generateValuesString($item);
            }
            $query .= implode(', ', $valuesStrings);
        } else {
            $query .= $this->generateValuesString($data);
        }
        return $query;
    }
    
    protected function generateValuesString($data)
    {
        return ' (' . implode(', ', array_map(function ($v) {
            return '?';
        }, $data)) . ')';
    }
    
    protected function bindValues($data, $multiDimensional = false)
    {
        $i = 1;
        foreach ($data as $item) {
            if ($multiDimensional) {
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
    
    public function add($queryType, $tableName, $data = [])
    {
        if (empty($tableName) || empty($data)) {
            throw new \ErrorException('No data specified');
        }
        
        $multiDimensional = is_array($data[key($data)]);
        
        list($keys, $data) = $this->getKeysValues($data, $multiDimensional);
        
        $query = $this->generateAddQuery($queryType, $tableName, $keys, $data, $multiDimensional);
        
        try {
            $this->db->beginTransaction();
            $this->stmt = $this->db->prepare($query);
            $this->bindValues($data, $multiDimensional);
            $this->stmt->execute();
            $this->db->commit();
            return $this->stmt->rowCount();
        } catch (\PDOException $e) {
            $this->db->rollBack();
            throw new \ErrorException($e->getMessage());
        }
    }
    
    public function insert($tableName, $data = [])
    {
        return $this->add(self::QUERY_TYPE_INSERT, $tableName, $data);
    }
    
    public function replace($tableName, $data = [])
    {
        return $this->add(self::QUERY_TYPE_REPLACE, $tableName, $data);
    }
}
