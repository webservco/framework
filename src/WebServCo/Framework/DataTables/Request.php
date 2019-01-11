<?php
namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\ArrayObject\Items;

class Request
{
    protected $draw;

    protected $columns;

    protected $order;

    protected $start;

    protected $length;

    protected $search;

    public function __construct($draw, Items $columns, Items $order, $start, $length, Search $search)
    {
        $this->draw = (int) $draw;
        $this->columns = $columns;
        $this->order = $order;
        $this->start = (int) $start;
        $this->length = (int) $length;
        $this->search = $search;
    }

    public function getColumns()
    {
        return $this->columns->getArrayObject();
    }

    public function getDraw()
    {
        return $this->draw;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getOrder()
    {
        return $this->order->getArrayObject();
    }

    public function getStart()
    {
        return $this->start;
    }
}
