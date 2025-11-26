<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

use Countable;
use Traversable;

/**
* @extends \Traversable<int|string,mixed>
*/
interface ArrayObjectInterface extends Traversable, Countable
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

    public function offsetGet(string|int $key): mixed;

    /**
    * @return void
    */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    public function offsetSet(int|string|null $key, mixed $value);

    /**
    * @return void
    */
    // phpcs:ignore SlevomatCodingStandard.TypeHints.ReturnTypeHint.MissingNativeTypeHint
    public function offsetUnset(string|int $key);
}
