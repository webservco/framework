<?php

namespace WebServCo\Framework\DataTables;

class Order
{
    protected $column;
    protected $dir;

    public function __construct($column, $dir)
    {
        $this->column = $column;
        $this->dir = $dir;
    }

    public function getColumn()
    {
        return $this->column;
    }

    public function getDir()
    {
        return $this->dir;
    }
}
