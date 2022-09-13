<?php

namespace WebServCo\Framework\DataTables;

/**
* @extends \ArrayObject<int, \WebServCo\Framework\DataTables\Column>
*/
class ColumnArrayObject extends \ArrayObject
{
    /**
    * @param mixed $name
    * @param mixed $value
    */
    public function offsetSet($name, $value): void
    {
        if (!($value instanceof Column)) {
            throw new \InvalidArgumentException('Only Column objects allowed.');
        }
        parent::offsetSet($name, $value);
    }
}
