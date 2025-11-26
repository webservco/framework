<?php

declare(strict_types=1);

namespace WebServCo\Framework\Arrays;

use WebServCo\Framework\Exceptions\Validation\RequiredArgumentException;

use function array_key_exists;
use function get_object_vars;
use function sprintf;

abstract class AbstractValidatedClass
{
    /**
    * @return array<int,string>
    */
    abstract protected function getRequiredProperties(): array;

    /**
    * @param array<string,mixed> $data
    */
    public function __construct(array $data = [])
    {
        foreach (get_object_vars($this) as $property => $default) {
            if (!array_key_exists($property, $data)) {
                continue;
            }

            $this->{$property} = $data[$property];
        }
        $this->validate();
    }

    protected function validate(): bool
    {
        $required = $this->getRequiredProperties();
        foreach ($required as $item) {
            if (empty($this->{$item})) {
                throw new RequiredArgumentException(
                    sprintf('Empty required parameter: %s', $item),
                );
            }
        }

        return true;
    }
}
