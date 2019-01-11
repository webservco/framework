<?php
namespace WebServCo\Framework\ArrayObject;

class Items
{
    protected $arrayObject;

    public function __construct(\ArrayObject $arrayObject)
    {
        $this->arrayObject = $arrayObject;
    }

    public function getArrayObject()
    {
        return $this->arrayObject;
    }

    public function set($index, $item)
    {
        $this->arrayObject->offsetSet($index, $item);
        return true;
    }

    public function remove($index)
    {
        $this->arrayObject->offsetUnset($index);
        $this->rebuildIndex();
        return true;
    }

    protected function rebuildIndex()
    {
        $data = $this->arrayObject->getArrayCopy();
        $this->arrayObject->exchangeArray([]);
        foreach ($data as $item) {
            $this->set(null, $item);
        }
        return true;
    }
}
