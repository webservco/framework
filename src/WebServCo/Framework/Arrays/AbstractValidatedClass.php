<?php

namespace WebServCo\Framework\Arrays;

abstract class AbstractValidatedClass
{
    abstract protected function getRequiredProperties();

    public function __construct($data = [])
    {
        foreach (get_object_vars($this) as $property => $default) {
            if (array_key_exists($property, $data)) {
                $this->{$property} = $data[$property];
            }
        }
        $this->validate();
    }

    protected function validate()
    {
        $required = $this->getRequiredProperties();
        foreach ($required as $item) {
            if (empty($this->{$item})) {
                throw new \WebServCo\Framework\Exceptions\Validation\RequiredArgumentException(
                    sprintf('Empty required parameter: %s', $item)
                );
            }
        }
        return true;
    }
}
