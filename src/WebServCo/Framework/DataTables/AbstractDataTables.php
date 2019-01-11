<?php
namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\Database\Order as DatabaseOrder;

abstract class AbstractDataTables
{
    protected $db;

    abstract protected function getDatabaseColumnName($dataTablesColumnName);
    abstract protected function getQuery($searchQuery, $orderQuery, $limitQuery);
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

        $searchQuery = $this->getSearchQuery($columnArrayObject);

        $orderArrayObject = $request->getOrder();
        $orderQuery = $this->getOrderQuery($columnArrayObject, $orderArrayObject);

        $length = $request->getLength();
        if (-1 != $length) {
            $limitQuery = 'LIMIT ?, ?';
            $params[] = $request->getStart();
            $params[] = $length;
        }

        $pdoStatement = $this->db->query(
            $this->getQuery($searchQuery, $orderQuery, $limitQuery),
            $params
        );

        $data = $this->getData($columnArrayObject, $pdoStatement);

        $recordsFiltered = $this->getRecordsFiltered();

        $recordsTotal = $this->getRecordsTotal($recordsFiltered, $searchQuery);

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

    protected function getOrderQuery(ColumnArrayObject $columnArrayObject, OrderArrayObject $orderArrayObject)
    {
        $orderQuery = null;
        $orderTotal = $orderArrayObject->count();
        if (0 < $orderTotal) {
            $orderQuery = "ORDER BY";
            $items = [];
            foreach ($orderArrayObject as $order) {
                if ($columnArrayObject[$order->getColumn()]->getOrderable()) {
                    $dir = strtoupper($order->getDir());
                    $items[] = sprintf(
                        ' %s %s',
                        $this->getDatabaseColumnName($columnArrayObject[$order->getColumn()]->getData()),
                        in_array($dir, [DatabaseOrder::ASC, DatabaseOrder::DESC]) ? $dir : DatabaseOrder::ASC
                    );
                }
            }
            $orderQuery .= implode(",", $items);
        }
        return $orderQuery;
    }

    protected function getRecordsFiltered()
    {
        return $this->db->getColumn("SELECT FOUND_ROWS()", [], 0);
    }

    protected function getRecordsTotal($recordsFiltered, $searchQuery)
    {
        if (empty($searchQuery)) {
            return $recordsFiltered;
        }
        return $this->db->getColumn( // grand total - query without the search, order, limits
            $this->getRecordsTotalQuery(),
            []
        );
    }

    protected function getSearchQuery(\WebServCo\Framework\DataTables\ColumnArrayObject $columnArrayObject)
    {
        $searchQuery = null;
        foreach ($columnArrayObject as $column) {
            if ($column->getSearchable()) {
                $search = $column->getSearch();
                $searchValue = $search->getValue();
                if (!empty($searchValue)) {
                    $searchQuery .= sprintf(
                        " AND %s REGEXP '%s'",
                        $this->getDatabaseColumnName($column->getData()),
                        $searchValue
                    );
                }
            }
        }
        return $searchQuery;
    }
}
