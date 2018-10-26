<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\AbstractDatabase;

trait DatabaseAddQueryTrait
{
    abstract public function escapeIdentifier($string);

    final protected function generateAddQuery($queryType, $tableName, $addData = [], $updateData = [])
    {
        $multiDimensional = is_array($addData[key($addData)]);

        list($keys, $data) = $this->getKeysValues($addData);

        $query = $this->generateAddQueryPrefix($queryType);
        $query .= ' ' . $this->escapeIdentifier($tableName);
        $query .= $this->generateAddQueryFieldsPart($keys);
        $query .= $this->generateAddQueryValuesPart($data, $multiDimensional);

        if ($multiDimensional) {
            return $query;
        }

        $query .= $this->generateAddQueryUpdatePart($updateData);

        return $query;
    }

    final protected function getKeysValues($data = [])
    {
        $multiDimensional = is_array($data[key($data)]);
        if ($multiDimensional) {
            $keys = array_keys(call_user_func_array('array_merge', $data));
            // fill any missing keys with empty data
            $key_pair = array_combine($keys, array_fill(0, count($keys), null));
            $data = array_map(function ($e) use ($key_pair) {
                return array_merge((array) $key_pair, $e);
            }, $data);
        } else {
            $keys = array_keys($data);
        }

        return [$keys, $data];
    }

    final protected function generateAddQueryPrefix($queryType)
    {
        switch ($queryType) {
            case AbstractDatabase::QUERY_TYPE_REPLACE:
                $query = AbstractDatabase::QUERY_TYPE_REPLACE . ' INTO';
                break;
            case AbstractDatabase::QUERY_TYPE_INSERT_IGNORE:
                $query = AbstractDatabase::QUERY_TYPE_INSERT_IGNORE . ' INTO';
                break;
            case AbstractDatabase::QUERY_TYPE_INSERT:
            default:
                $query = AbstractDatabase::QUERY_TYPE_INSERT . ' INTO';
                break;
        }

        return $query;
    }

    final protected function generateAddQueryFieldsPart($fields)
    {
        return ' (' . implode(
            ', ',
            array_map([$this, 'escapeIdentifier'], $fields)
        ) .
        ')';
    }

    final protected function generateAddQueryValuesPart($data, $multiDimensional)
    {
        $query = ' VALUES';
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

    final protected function generateAddQueryUpdatePart($data = [])
    {
        if (empty($data)) {
            return false;
        }

        $strings = [];
        foreach ($data as $k => $v) {
            $strings[] = sprintf('%s = ?', $this->escapeIdentifier($k));
        }

        $query = " ON DUPLICATE KEY UPDATE ";
        $query .= implode(', ', $strings);
        return $query;
    }

    final protected function generateValuesString($data)
    {
        return ' (' . implode(', ', array_map(function () {
            return '?';
        }, $data)) . ')';
    }
}
