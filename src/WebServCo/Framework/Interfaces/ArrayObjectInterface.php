<?php

declare(strict_types=1);

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
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    public function exchangeArray(array|object $array);

    /**
    * @return array<int|string,mixed>
    */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    public function getArrayCopy();

    /**
     * @param string|int $key
     * @return mixed
     */
    public function offsetGet($key);

    /**
    * @param int|string|null $key
    * @param mixed $value
    * @return void
    */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    public function offsetSet($key, $value);

    /**
    * @param string|int $key
    * @return void
    */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    public function offsetUnset($key);
}
