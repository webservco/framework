<?php declare(strict_types = 1);

namespace WebServCo\Framework\ArrayObject;

use WebServCo\Framework\Interfaces\ArrayObjectInterface;

class Items
{
    protected ArrayObjectInterface $arrayObject;

    public function __construct(ArrayObjectInterface $arrayObject)
    {
        $this->arrayObject = $arrayObject;
    }

    public function getArrayObject(): ArrayObjectInterface
    {
        return $this->arrayObject;
    }

    /**
    * @param int|null|string $index
    * @param mixed $item
    * @return bool
    */
    public function set($index, $item): bool
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
