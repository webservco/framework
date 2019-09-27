<?php
namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\Database\Order as DatabaseOrder;

abstract class AbstractDataTablesDatabase implements \WebServCo\Framework\Interfaces\DataTablesInterface
{
    protected $db;

    abstract protected function getQuery($searchQueryPart, $orderQueryPart, $limitQuery);
    abstract protected function getRecordsTotalQuery();

    public function __construct(\WebServCo\Framework\Interfaces\DatabaseInterface $db)
    {
        $this->db = $db;
    }

    public function getResponse(Request $request)
    {
        $params = [];
        $limitQuery = null;

        $columnArrayObject = $request->getColumns();

        list($searchQueryPart, $searchParams) = $this->getSearchQueryPart($columnArrayObject);
        $params = array_merge($params, $searchParams);

        $orderArrayObject = $request->getOrder();
        $orderQueryPart = $this->getOrderQueryPart($columnArrayObject, $orderArrayObject);

        $length = $request->getLength();
        if (-1 != $length) {
            $limitQuery = 'LIMIT ?, ?';
            $params[] = $request->getStart();
            $params[] = $length;
        }

        $query = $this->getQuery($searchQueryPart, $orderQueryPart, $limitQuery);
        $pdoStatement = $this->db->query($query, $params);

        $data = $this->getData($columnArrayObject, $pdoStatement);

        $recordsFiltered = $this->getRecordsFiltered();

        $recordsTotal = $this->getRecordsTotal($recordsFiltered, $searchQueryPart);

        return new Response(
            $request->getDraw(),
            $recordsTotal,
            $recordsFiltered,
            $data
        );
    }

    protected function getData(ColumnArrayObject $columnArrayObject, \PDOStatement $pdoStatement)
    {
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

    protected function getDatabaseColumnName($dataTablesColumnName)
    {
        return $this->db->escapeIdentifier($dataTablesColumnName);
    }

    protected function getOrderQueryPart(ColumnArrayObject $columnArrayObject, OrderArrayObject $orderArrayObject)
    {
        $query = null;
        $orderTotal = $orderArrayObject->count();
        if (0 < $orderTotal) {
            $query = "ORDER BY";
            $items = [];
            foreach ($orderArrayObject as $order) {
                if ($columnArrayObject[$order->getColumn()]->getOrderable()) {
                    $dir = strtoupper($order->getDir());
                    $dir = in_array($dir, [DatabaseOrder::ASC, DatabaseOrder::DESC]) ? $dir : DatabaseOrder::ASC;
                    $columnName = $this->getDatabaseColumnName($columnArrayObject[$order->getColumn()]->getData());
                    $items[] = sprintf(
                        ' LENGTH(%s) %s , %s %s', // mimic natural sorting
                        $columnName,
                        $dir,
                        $columnName,
                        $dir
                    );
                }
            }
            $query .= implode(",", $items);
        }
        return $query;
    }

    protected function getRecordsFiltered()
    {
        return $this->db->getColumn("SELECT FOUND_ROWS()", [], 0);
    }

    protected function getRecordsTotal($recordsFiltered, $searchQueryPart)
    {
        if (empty($searchQueryPart)) {
            return $recordsFiltered;
        }
        return $this->db->getColumn( // grand total - query without the search, order, limits
            $this->getRecordsTotalQuery(),
            []
        );
    }

    protected function getSearchQueryPart(ColumnArrayObject $columnArrayObject)
    {
        $query = null;
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
