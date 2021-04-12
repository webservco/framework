<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Database\QueryType;
use WebServCo\Framework\Utils\Arrays;

trait DatabaseAddQueryTrait
{

    abstract public function escapeIdentifier(string $string): string;

    abstract public function escapeTableName(string $string): string;

    /**
    * @param array<mixed> $addData
    * @param array<mixed> $updateData
    */
    final protected function generateAddQuery(
        string $queryType,
        string $tableName,
        array $addData = [],
        array $updateData = []
    ): string {
        $multiDimensional = Arrays::isMultidimensional($addData);

        [$keys, $data] = $this->getKeysValues($addData);

        $query = $this->generateAddQueryPrefix($queryType);
        $query .= ' ' . $this->escapeTableName($tableName);
        $query .= $this->generateAddQueryFieldsPart($keys);
        $query .= $this->generateAddQueryValuesPart($data, $multiDimensional);

        if ($multiDimensional) {
            return $query;
        }

        return $query . $this->generateAddQueryUpdatePart($updateData);
    }

    /**
    * @param array<mixed> $data
    * @return array<mixed>
    */
    final protected function getKeysValues(array $data = []): array
    {
        $multiDimensional = Arrays::isMultidimensional($data);
        if ($multiDimensional) {
            $keys = \array_keys(\call_user_func_array('array_merge', $data));
            // fill any missing keys with empty data
            $key_pair = \array_combine($keys, \array_fill(0, \count($keys), null));
            $data = \array_map(static fn ($e) => \array_merge((array) $key_pair, $e), $data);
        } else {
            $keys = \array_keys($data);
        }

        return [$keys, $data];
    }

    final protected function generateAddQueryPrefix(string $queryType): string
    {
        switch ($queryType) {
            case QueryType::REPLACE:
                $query = QueryType::REPLACE . ' INTO';
                break;
            case QueryType::INSERT_IGNORE:
                $query = QueryType::INSERT_IGNORE . ' INTO';
                break;
            case QueryType::INSERT:
            default:
                $query = QueryType::INSERT . ' INTO';
                break;
        }

        return $query;
    }

    /**
    * @param array<int,string> $fields
    */
    final protected function generateAddQueryFieldsPart(array $fields): string
    {
        return ' (' . \implode(
            ', ',
            \array_map([$this, 'escapeIdentifier'], $fields),
        ) .
        ')';
    }

    /**
    * @param array<mixed> $data
    */
    final protected function generateAddQueryValuesPart(array $data, bool $multiDimensional): string
    {
        $query = ' VALUES';
        if ($multiDimensional) {
            $valuesStrings = [];
            foreach ($data as $item) {
                $valuesStrings[] = $this->generateValuesString($item);
            }
            $query .= \implode(', ', $valuesStrings);
        } else {
            $query .= $this->generateValuesString($data);
        }
        return $query;
    }

    /**
    * @param array<mixed> $data
    */
    final protected function generateAddQueryUpdatePart(array $data = []): string
    {
        if (!$data) {
            return '';
        }

        $strings = [];
        foreach ($data as $k => $v) {
            $strings[] = \sprintf('%s = ?', $this->escapeIdentifier((string) $k));
        }

        $query = " ON DUPLICATE KEY UPDATE ";
        $query .= \implode(', ', $strings);
        return $query;
    }

    /**
    * @param array<int,float|int|string> $data
    */
    final protected function generateValuesString(array $data): string
    {
        $placeholdersString = \WebServCo\Framework\Database\Utils\PreparedStatements::generatePlaceholdersString($data);
        return ' (' . $placeholdersString . ')';
    }
}
