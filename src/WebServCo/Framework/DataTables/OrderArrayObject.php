<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

use ArrayObject;
use InvalidArgumentException;
use WebServCo\Framework\Interfaces\ArrayObjectInterface;

/**
* @extends \ArrayObject<int, \WebServCo\Framework\DataTables\Order>
*/
final class OrderArrayObject extends ArrayObject implements ArrayObjectInterface
{
    public function offsetSet(mixed $name, mixed $value): void
    {
        if (!($value instanceof Order)) {
            throw new InvalidArgumentException('Only Order objects allowed.');
        }

        parent::offsetSet($name, $value);
    }
}
