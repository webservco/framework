<?php

declare(strict_types=1);

namespace WebServCo\Framework\ArrayObject;

use WebServCo\Framework\Interfaces\ArrayObjectInterface;

final class Items
{
    public function __construct(protected ArrayObjectInterface $arrayObject)
    {
    }

    public function getArrayObject(): ArrayObjectInterface
    {
        return $this->arrayObject;
    }

    public function set(int|string|null $index, mixed $item): bool
    {
        $this->arrayObject->offsetSet($index, $item);

        return true;
    }

    public function remove(int $index): bool
    {
        $this->arrayObject->offsetUnset($index);
        $this->rebuildIndex();

        return true;
    }

    protected function rebuildIndex(): bool
    {
        $data = $this->arrayObject->getArrayCopy();
        $this->arrayObject->exchangeArray([]);
        foreach ($data as $item) {
            $this->set(null, $item);
        }

        return true;
    }
}
