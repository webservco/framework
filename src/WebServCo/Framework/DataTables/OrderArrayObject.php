<?php
namespace WebServCo\Framework\DataTables;

class OrderArrayObject extends \ArrayObject
{
    public function offsetSet($name, $value)
    {
        if (!($value instanceof Order)) {
            throw new \InvalidArgumentException('Only Order objects allowed.');
        }
        parent::offsetSet($name, $value);
    }
}
