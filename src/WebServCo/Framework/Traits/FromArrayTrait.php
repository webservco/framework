<?php declare(strict_types = 1);

namespace WebServCo\Framework\Traits;

trait FromArrayTrait
{

    /**
    * Returns an instance of the current class
    *
    * @param array<string,mixed> $data
    * @return mixed
    */
    public static function fromArray(array $data = [])
    {
        $object = new static();
        foreach (\get_object_vars($object) as $property => $default) {
            if (!\array_key_exists($property, $data)) {
                continue;
            }

            $object->{$property} = $data[$property];
        }
        return $object;
    }
}
