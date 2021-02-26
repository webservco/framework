<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\Database\Order as DatabaseOrder;
use WebServCo\Framework\Exceptions\DatabaseException;
use WebServCo\Framework\Interfaces\ArrayObjectInterface;
use WebServCo\Framework\Interfaces\DatabaseInterface;

abstract class AbstractDataTablesDatabase implements \WebServCo\Framework\Interfaces\DataTablesInterface
{

    protected DatabaseInterface $db;

    /**
    * @param string $orderPart,
    */
    abstract protected function getQuery(string $searchPart, string $orderPart, string $limitPart): string;

    abstract protected function getRecordsTotalQuery(): string;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getResponse(Request $request): Response
    {
        $params = [];
        $limitPart = '';

        $columnArrayObject = $request->getColumns();
        [$searchPart, $searchParams] = $this->getSearchQueryPart($columnArrayObject);

        if (!\is_string($searchPart)) {
            throw new \InvalidArgumentException('"searchQueryPart" is not a string');
        }
        if (!\is_array($searchParams)) {
            throw new \InvalidArgumentException('"searchParams" is not an array');
        }

        $params = \array_merge($params, $searchParams);

        $orderArrayObject = $request->getOrder();
        $orderPart = $this->getOrderQueryPart($columnArrayObject, $orderArrayObject);

        $length = $request->getLength();
        if (-1 !== $length) {
            $limitPart = 'LIMIT ?, ?';
            $params[] = $request->getStart();
            $params[] = $length;
        }
        $query = $this->getQuery($searchPart, $orderPart, $limitPart);
        try {
            $pdoStatement = $this->db->query($query, $params);
        } catch (DatabaseException $e) {
            // Rethrow in order to pinpoint the query location in the logs.
            throw new DatabaseException($e->getMessage(), $e);
        }

        $data = $this->getData($columnArrayObject, $pdoStatement);

        $recordsFiltered = $this->getRecordsFiltered();


        $recordsTotal = $this->getRecordsTotal($recordsFiltered, $searchPart);

        return new Response(
            $request->getDraw(),
            $recordsTotal,
            $recordsFiltered,
            $data,
        );
    }

    protected function assertColumnArrayObject(ArrayObjectInterface $arrayObject): bool
    {
        if (!$arrayObject instanceof ColumnArrayObject) {
            throw new \InvalidArgumentException(\sprintf('Object is not an instance of %s.', 'ColumnArrayObject'));
        }
        return true;
    }

    protected function assertOrderArrayObject(ArrayObjectInterface $arrayObject): bool
    {
        if (!$arrayObject instanceof OrderArrayObject) {
            throw new \InvalidArgumentException(\sprintf('Object is not an instance of %s.', 'OrderArrayObject'));
        }
        return true;
    }

    /**
    * @return array<int,array<int|string,mixed>>
    */
    protected function getData(ArrayObjectInterface $columnArrayObject, \PDOStatement $pdoStatement): array
    {
        $this->assertColumnArrayObject($columnArrayObject);
        $data = [];
        while ($row = $pdoStatement->fetch(\PDO::FETCH_ASSOC)) {
            $item = [];
            foreach ($columnArrayObject as $column) {
                $name = $column->getData();
                $item[$name] = $row[$name] ?? null;
            }
            $data[] = $item;
        }
        return $data;
    }

    protected function getDatabaseColumnName(string $dataTablesColumnName): string
    {
        try {
            return $this->db->escapeIdentifier($dataTablesColumnName);
        } catch (DatabaseException $e) {
            // Rethrow in order to pinpoint the query location in the logs.
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    protected function getOrderQueryPart(
        ArrayObjectInterface $columnArrayObject,
        ArrayObjectInterface $orderArrayObject
    ): string {
        $this->assertColumnArrayObject($columnArrayObject);
        $this->assertOrderArrayObject($orderArrayObject);
        $query = '';
        $orderTotal = $orderArrayObject->count();
        if (0 < $orderTotal) {
            $query = "ORDER BY";
            $items = [];
            foreach ($orderArrayObject as $order) {
                $columnKey = (string) $order->getColumn();
                $column = $columnArrayObject->offsetGet($columnKey);
                if (!($column instanceof Column)) {
                    continue;
                }

                if (!$column->getOrderable()) {
                    continue;
                }

                $dir = \strtoupper($order->getDir());
                $dir = \in_array($dir, [DatabaseOrder::ASC, DatabaseOrder::DESC], true)
                    ? $dir
                    : DatabaseOrder::ASC;
                $columnName = $this->getDatabaseColumnName($column->getData());
                $items[] = \sprintf(' %s %s', $columnName, $dir);
            }
            $query .= \implode(",", $items);
        }
        return $query;
    }

    protected function getRecordsFiltered(): int
    {
        try {
            return (int) $this->db->getColumn("SELECT FOUND_ROWS()", [], 0);
        } catch (DatabaseException $e) {
            // Rethrow in order to pinpoint the query location in the logs.
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    protected function getRecordsTotal(int $recordsFiltered, string $searchPart): int
    {
        if (empty($searchPart)) {
            return $recordsFiltered;
        }
        try {
            return (int) $this->db->getColumn( // grand total - query without the search, order, limits
                $this->getRecordsTotalQuery(),
                [],
            );
        } catch (DatabaseException $e) {
            // Rethrow in order to pinpoint the query location in the logs.
            throw new DatabaseException($e->getMessage(), $e);
        }
    }

    /**
    * @return array<int,array<int,string>|string>
    */
    protected function getSearchQueryPart(ArrayObjectInterface $columnArrayObject): array
    {
        $this->assertColumnArrayObject($columnArrayObject);
        $query = '';
        $params = [];
        foreach ($columnArrayObject as $column) {
            if (!$column->getSearchable()) {
                continue;
            }

            $search = $column->getSearch();
            $searchValue = $search->getValue();
            if ('' === $searchValue) {
                continue;
            }
            // make sure it works also for "0"
            $query .= \sprintf(
                " AND %s LIKE ?",
                $this->getDatabaseColumnName($column->getData()),
            );
            $params[] = \sprintf('%%%s%%', $searchValue);
        }
        return [$query, $params];
    }
}
