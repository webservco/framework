<?php

namespace WebServCo\Framework\DataTables;

class ColumnArrayObject extends \ArrayObject
{
    public function offsetSet($name, $value)
    {
        if (!($value instanceof Column)) {
            throw new \InvalidArgumentException('Only Column objects allowed.');
        }
        parent::offsetSet($name, $value);
    }
}
