<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

final class Column
{
    public function __construct(
        protected string $data,
        protected string $name,
        protected bool $searchable,
        protected bool $orderable,
        protected Search $search,
    ) {
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
