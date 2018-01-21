<?php
namespace WebServCo\Framework\Traits;

trait DatabaseTrait
{
    public function insert($tableName, $data = [])
    {
        return $this->add(self::QUERY_TYPE_INSERT, $tableName, $data);
    }
    
    public function replace($tableName, $data = [])
    {
        return $this->add(self::QUERY_TYPE_REPLACE, $tableName, $data);
    }
    
    protected function add($queryType, $tableName, $data = [])
    {
        if (empty($tableName) || empty($data)) {
            throw new \ErrorException('No data specified');
        }
        
        $query = $this->generateAddQuery($queryType, $tableName, $data);
        
        return $this->executeQuery($query, $data);
    }
    
    protected function getKeysValues($data = [])
    {
        $multiDimensional = is_array($data[key($data)]);
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
    
    protected function generateAddQuery($queryType, $tableName, $data)
    {
        $multiDimensional = is_array($data[key($data)]);
        
        list($keys, $data) = $this->getKeysValues($data);
        
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
        $query .= ' '.$this->escapeIdentifier($tableName).' (' .
        implode(', ', array_map([$this, 'escapeIdentifier'], $keys)) .
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
}
