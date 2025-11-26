<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

final class Order
{
    public function __construct(protected string $column, protected string $dir)
    {
    }

    public function getColumn(): string
    {
        return $this->column;
    }

    public function getDir(): string
    {
        return $this->dir;
    }
}
