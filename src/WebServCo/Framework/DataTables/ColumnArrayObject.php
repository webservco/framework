<?php
namespace WebServCo\Framework\DataTables;

/**
* @extends \ArrayObject<int,Column>
*/
class ColumnArrayObject extends \ArrayObject implements \WebServCo\Framework\Interfaces\ArrayObjectInterface
{
    /**
    * @param mixed $name
    * @param mixed $value
    * @return void
    */
    public function offsetSet($name, $value): void
    {
        if (!($value instanceof Column)) {
            throw new \InvalidArgumentException('Only Column objects allowed.');
        }
        parent::offsetSet($name, $value);
    }
}
