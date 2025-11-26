<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

use ArrayObject;
use InvalidArgumentException;
use WebServCo\Framework\Interfaces\ArrayObjectInterface;

/**
* @extends \ArrayObject<int, \WebServCo\Framework\DataTables\Column>
*/
final class ColumnArrayObject extends ArrayObject implements ArrayObjectInterface
{
    public function offsetSet(mixed $name, mixed $value): void
    {
        if (!($value instanceof Column)) {
            throw new InvalidArgumentException('Only Column objects allowed.');
        }

        parent::offsetSet($name, $value);
    }
}
