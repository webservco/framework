<?php
namespace WebServCo\Framework\DataTables;

/**
* @extends \ArrayObject<int,Order>
*/
class OrderArrayObject extends \ArrayObject implements \WebServCo\Framework\Interfaces\ArrayObjectInterface
{
    /**
    * @param mixed $name
    * @param mixed $value
    * @return void
    */
    public function offsetSet($name, $value): void
    {
        if (!($value instanceof Order)) {
            throw new \InvalidArgumentException('Only Order objects allowed.');
        }
        parent::offsetSet($name, $value);
    }
}
