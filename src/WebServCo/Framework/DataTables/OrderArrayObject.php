<?php

namespace WebServCo\Framework\DataTables;

/**
* @extends \ArrayObject<int, \WebServCo\Framework\DataTables\Order>
*/
class OrderArrayObject extends \ArrayObject
{
    /**
    * @param mixed $name
    * @param mixed $value
    */
    public function offsetSet($name, $value): void
    {
        if (!($value instanceof Order)) {
            throw new \InvalidArgumentException('Only Order objects allowed.');
        }
        parent::offsetSet($name, $value);
    }
}
