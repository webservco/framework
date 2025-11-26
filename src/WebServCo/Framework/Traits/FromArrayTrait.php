<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use function array_key_exists;
use function get_object_vars;

trait FromArrayTrait
{
    /**
    * Returns an instance of the current class
    *
    * @param array<string,mixed> $data
    */
    public static function fromArray(array $data = []): mixed
    {
        $object = new static();
        foreach (get_object_vars($object) as $property => $default) {
            if (!array_key_exists($property, $data)) {
                continue;
            }

            $object->{$property} = $data[$property];
        }

        return $object;
    }
}
