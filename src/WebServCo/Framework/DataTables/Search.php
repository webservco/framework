<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

final class Search
{
    public function __construct(protected string $value, protected bool $regex)
    {
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
