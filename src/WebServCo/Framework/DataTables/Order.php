<?php
namespace WebServCo\Framework\DataTables;

class Order
{
    protected string $column;
    protected string $dir;

    public function __construct(string $column, string $dir)
    {
        $this->column = $column;
        $this->dir = $dir;
    }

    public function getColumn() : string
    {
        return $this->column;
    }

    public function getDir() : string
    {
        return $this->dir;
    }
}
