<?php
namespace WebServCo\Framework\DataTables;

class Column
{
    protected $data;
    protected $name;
    protected $searchable;
    protected $orderable;
    protected $search;

    public function __construct($data, $name, $searchable, $orderable, Search $search)
    {
        $this->data = $data;
        $this->name = $name;
        $this->searchable = (bool) $searchable;
        $this->orderable = (bool) $orderable;
        $this->search = $search;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getOrderable()
    {
        return $this->orderable;
    }

    public function getSearch()
    {
        return $this->search;
    }

    public function getSearchable()
    {
        return $this->searchable;
    }
}
