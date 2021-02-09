<?php declare(strict_types = 1);

namespace WebServCo\Framework\DataTables;

class Search
{

    protected string $value;
    protected bool $regex;

    public function __construct(string $value, bool $regex)
    {
        $this->value = $value;
        $this->regex = $regex;
    }

    public function getValue(): string
    {
        return $this->value;
    }
}
