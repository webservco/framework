<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\AbstractDatabase as Db;

trait DatabaseTrait
{
    use DatabaseAddQueryTrait;
    
    public function insert($tableName, $addData = [], $updateData = [])
    {
        return $this->add(Db::QUERY_TYPE_INSERT, $tableName, $addData, $updateData);
    }
    
    public function insertIgnore($tableName, $data = [])
    {
        return $this->add(Db::QUERY_TYPE_INSERT_IGNORE, $tableName, $data);
    }
    
    public function replace($tableName, $data = [])
    {
        return $this->add(Db::QUERY_TYPE_REPLACE, $tableName, $data);
    }
    
    protected function add($queryType, $tableName, $addData = [], $updateData = [])
    {
        if (empty($tableName) || empty($addData)) {
            throw new \ErrorException('No data specified');
        }
        
        $query = $this->generateAddQuery($queryType, $tableName, $addData, $updateData);
        
        $queryData = [];
        foreach ($addData as $item) {
            $queryData[] = $item;
        }
        foreach ($updateData as $item) {
            $queryData[] = $item;
        }
        
        return $this->query($query, $queryData);
    }
    
    public function valueExists($table, $field, $value)
    {
        return (bool) $this->getColumn(
            sprintf(
                "SELECT 1 FROM %s WHERE %s = ? LIMIT 1",
                $this->escapeIdentifier($table),
                $this->escapeIdentifier($field)
            ),
            [$value]
        );
    }
}
