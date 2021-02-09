<?php declare(strict_types = 1);

namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\ArrayObject\Items;
use WebServCo\Framework\Interfaces\ArrayObjectInterface;

class Request
{

    protected int $draw;

    protected Items $columns;

    protected Items $order;

    protected int $start;

    protected int $length;

    protected Search $search;

    public function __construct(int $draw, Items $columns, Items $order, int $start, int $length, Search $search)
    {
        $this->draw = $draw;
        $this->columns = $columns;
        $this->order = $order;
        $this->start = $start;
        $this->length = $length;
        $this->search = $search;
    }

    /**
    * @return \WebServCo\Framework\Interfaces\ArrayObjectInterface<\WebServCo\Framework\DataTables\ColumnArrayObject>
    */
    public function getColumns(): ArrayObjectInterface
    {
        return $this->columns->getArrayObject();
    }

    public function getDraw(): int
    {
        return $this->draw;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    /**
    * @return \WebServCo\Framework\Interfaces\ArrayObjectInterface<\WebServCo\Framework\DataTables\OrderArrayObject>
    */
    public function getOrder(): ArrayObjectInterface
    {
        return $this->order->getArrayObject();
    }

    public function getStart(): int
    {
        return $this->start;
    }
}
