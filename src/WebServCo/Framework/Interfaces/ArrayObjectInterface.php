<?php declare(strict_types = 1);

namespace WebServCo\Framework\Interfaces;

/**
* @extends \Traversable<int|string,mixed>
*/
interface ArrayObjectInterface extends \Traversable, \Countable
{
    /**
    * @param array<mixed>|object $array
    * @return array<int|string,mixed>|null
    */
    public function exchangeArray($array);

    /**
    * @return array<int|string,mixed>
    */
    public function getArrayCopy();

    /**
     * @param string|int $key
     * @return mixed
     */
    public function offsetGet($key);

    /**
    * @param int|null|string $key
    * @param mixed $value
    * @return void
    */
    public function offsetSet($key, $value);

    /**
    * @param string|int $key
    * @return void
    */
    public function offsetUnset($key);
}
