<?php
namespace WebServCo\Framework;

abstract class AbstractClassFromArray
{
    public function __construct($data)
    {
        foreach (get_object_vars($this) as $property => $default) {
            if (array_key_exists($property, $data)) {
                $this->{$property} = $data[$property];
            }
        }
    }
}
