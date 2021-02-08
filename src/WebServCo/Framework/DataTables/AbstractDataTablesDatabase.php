<?php declare(strict_types = 1);

namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\Database\Order as DatabaseOrder;
use WebServCo\Framework\Interfaces\ArrayObjectInterface;
use WebServCo\Framework\Interfaces\DatabaseInterface;

abstract class AbstractDataTablesDatabase implements \WebServCo\Framework\Interfaces\DataTablesInterface
{
    protected DatabaseInterface $db;

    /**
    * @param string $searchPart
    * @param string $orderPart,
    * @param string $limitPart
    * @return string
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
        list($searchPart, $searchParams) = $this->getSearchQueryPart($columnArrayObject);

        if (!is_string($searchPart)) {
            throw new \InvalidArgumentException('"searchQueryPart" is not a string');
        }
        if (!is_array($searchParams)) {
            throw new \InvalidArgumentException('"searchParams" is not an array');
        }

        $params = array_merge($params, $searchParams);

        $orderArrayObject = $request->getOrder();
        $orderPart = $this->getOrderQueryPart($columnArrayObject, $orderArrayObject);

        $length = $request->getLength();
        if (-1 != $length) {
            $limitPart = 'LIMIT ?, ?';
            $params[] = $request->getStart();
            $params[] = $length;
        }
        $query = $this->getQuery($searchPart, $orderPart, $limitPart);
        $pdoStatement = $this->db->query($query, $params);

        $data = $this->getData($columnArrayObject, $pdoStatement);

        $recordsFiltered = $this->getRecordsFiltered();


        $recordsTotal = $this->getRecordsTotal($recordsFiltered, $searchPart);

        return new Response(
            $request->getDraw(),
            $recordsTotal,
            $recordsFiltered,
            $data
        );
    }

    protected function assertColumnArrayObject(ArrayObjectInterface $arrayObject): bool
    {
        if (!$arrayObject instanceof ColumnArrayObject) {
            throw new \InvalidArgumentException(sprintf('Object is not an instance of %s.', 'ColumnArrayObject'));
        }
        return true;
    }

    protected function assertOrderArrayObject(ArrayObjectInterface $arrayObject): bool
    {
        if (!$arrayObject instanceof OrderArrayObject) {
            throw new \InvalidArgumentException(sprintf('Object is not an instance of %s.', 'OrderArrayObject'));
        }
        return true;
    }

    /**
    * @param ArrayObjectInterface $columnArrayObject
    * @param \PDOStatement $pdoStatement
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
                $item[$name] = isset($row[$name]) ? $row[$name] : null;
            }
            $data[] = $item;
        }
        return $data;
    }

    protected function getDatabaseColumnName(string $dataTablesColumnName): string
    {
        return $this->db->escapeIdentifier($dataTablesColumnName);
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
                if ($column instanceof Column) {
                    if ($column->getOrderable()) {
                        $dir = strtoupper($order->getDir());
                        $dir = in_array($dir, [DatabaseOrder::ASC, DatabaseOrder::DESC]) ? $dir : DatabaseOrder::ASC;
                        $columnName = $this->getDatabaseColumnName($column->getData());
                        $items[] = sprintf(
                            ' %s %s',
                            $columnName,
                            $dir
                        );
                    }
                }
            }
            $query .= implode(",", $items);
        }
        return $query;
    }

    protected function getRecordsFiltered(): int
    {
        return (int) $this->db->getColumn("SELECT FOUND_ROWS()", [], 0);
    }

    protected function getRecordsTotal(int $recordsFiltered, string $searchPart): int
    {
        if (empty($searchPart)) {
            return $recordsFiltered;
        }
        return (int) $this->db->getColumn( // grand total - query without the search, order, limits
            $this->getRecordsTotalQuery(),
            []
        );
    }

    /**
    * @param ArrayObjectInterface $columnArrayObject
    * @return array<int,array<int,string>|string>
    */
    protected function getSearchQueryPart(ArrayObjectInterface $columnArrayObject): array
    {
        $this->assertColumnArrayObject($columnArrayObject);
        $query = '';
        $params = [];
        foreach ($columnArrayObject as $column) {
            if ($column->getSearchable()) {
                $search = $column->getSearch();
                $searchValue = $search->getValue();
                if ('' !== $searchValue) { // make sure it works also for "0"
                    $query .= sprintf(
                        " AND %s LIKE ?",
                        $this->getDatabaseColumnName($column->getData())
                    );
                    $params[] = sprintf('%%%s%%', $searchValue);
                }
            }
        }
        return [$query, $params];
    }
}
