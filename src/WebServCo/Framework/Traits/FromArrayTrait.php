<?php
namespace WebServCo\Framework\Traits;

trait FromArrayTrait
{
    /**
    * Returns an instance of the current class
    * @param array<string,mixed>
    * @return mixed
    */
    public static function fromArray(array $data = [])
    {
        $object = new static();
        foreach (get_object_vars($object) as $property => $default) {
            if (array_key_exists($property, $data)) {
                $object->{$property} = $data[$property];
            }
        }
        return $object;
    }
}
