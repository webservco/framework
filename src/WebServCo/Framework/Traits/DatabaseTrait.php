<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\AbstractDatabase as Db;

trait DatabaseTrait
{
    use DatabaseAddQueryTrait;

    abstract public function getColumn($query, $params = [], $columnNumber = 0);
    abstract public function query($query, $values = []);

    final public function insert($tableName, $addData = [], $updateData = [])
    {
        return $this->add(Db::QUERY_TYPE_INSERT, $tableName, $addData, $updateData);
    }

    final public function insertIgnore($tableName, $data = [])
    {
        return $this->add(Db::QUERY_TYPE_INSERT_IGNORE, $tableName, $data);
    }

    final public function replace($tableName, $data = [])
    {
        return $this->add(Db::QUERY_TYPE_REPLACE, $tableName, $data);
    }

    final protected function add($queryType, $tableName, $addData = [], $updateData = [])
    {
        if (empty($tableName) || empty($addData)) {
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
                $this->escapeIdentifier($table),
                $this->escapeIdentifier($field)
            ),
            [$value]
        );
    }

    final public function tableExists($table, $database = null)
    {
        $name = $this->escapeIdentifier($table);
        if (!empty($database)) {
            $name = sprintf('%s.%s', $this->escapeIdentifier($database), $this->escapeIdentifier($table));
        }

        try {
            $this->query(sprintf('SELECT 1 FROM %s LIMIT 1', $name));
            return true;
        //} catch (\Exception $e) {
            //return false;
        } catch (\WebServCo\Framework\Exceptions\DatabaseException $e) {
            return false;
        }
    }
}
