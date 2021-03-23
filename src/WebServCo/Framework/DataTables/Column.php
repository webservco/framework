<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

class Column
{

    protected string $data;
    protected string $name;
    protected bool $searchable;
    protected bool $orderable;
    protected Search $search;

    public function __construct(string $data, string $name, bool $searchable, bool $orderable, Search $search)
    {
        $this->data = $data;
        $this->name = $name;
        $this->searchable = $searchable;
        $this->orderable = $orderable;
        $this->search = $search;
    }

    public function getData(): string
    {
        return $this->data;
    }

    public function getOrderable(): bool
    {
        return $this->orderable;
    }

    public function getSearch(): Search
    {
        return $this->search;
    }

    public function getSearchable(): bool
    {
        return $this->searchable;
    }
}
