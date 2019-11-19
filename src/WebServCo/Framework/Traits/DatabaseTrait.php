<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Database\QueryType;

trait DatabaseTrait
{
    abstract public function escapeIdentifier($string);
    abstract public function escapeTableName($string);
    abstract protected function generateAddQuery($queryType, $tableName, $addData = [], $updateData = []);
    abstract public function getColumn($query, $params = [], $columnNumber = 0);
    abstract public function query($query, $values = []);

    final public function insert($tableName, $addData = [], $updateData = [])
    {
        return $this->add(QueryType::INSERT, $tableName, $addData, $updateData);
    }

    final public function insertIgnore($tableName, $data = [])
    {
        return $this->add(QueryType::INSERT_IGNORE, $tableName, $data);
    }

    final public function replace($tableName, $data = [])
    {
        return $this->add(QueryType::REPLACE, $tableName, $data);
    }

    final protected function add($queryType, $tableName, $addData = [], $updateData = [])
    {
        if (empty($tableName)) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException('No data specified');
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

    final public function valueExists($table, $field, $value)
    {
        return (bool) $this->getColumn(
            sprintf(
                "SELECT 1 FROM %s WHERE %s = ? LIMIT 1",
                $this->escapeTableName($table),
                $this->escapeIdentifier($field)
            ),
            [$value]
        );
    }

    final public function tableExists($table)
    {
        $name = $this->escapeTableName($table);

        try {
            $this->query(sprintf('SELECT 1 FROM %s LIMIT 1', $name));
            return true;
        } catch (\WebServCo\Framework\Exceptions\DatabaseException $e) {
            return false;
        }
    }
}
