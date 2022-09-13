<?php

namespace WebServCo\Framework\DataTables;

class Search
{
    protected $value;
    protected $regex;

    public function __construct($value, $regex)
    {
        $this->value = $value;
        $this->regex = (bool) $regex;
    }

    public function getValue()
    {
        return $this->value;
    }
}
