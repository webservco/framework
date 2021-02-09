<?php declare(strict_types = 1);

namespace WebServCo\Framework\DataTables;

abstract class AbstractHelper
{

    /**
    * @param array<string,mixed> $data
    * @param array<int,string> $required
    */
    public static function validate(array $data, array $required = []): bool
    {
        if (!\is_array($data)) {
            throw new \InvalidArgumentException('Data is not an array.');
        }

        foreach ($required as $item) {
            if (!isset($data[$item])) {
                throw new \InvalidArgumentException(\sprintf('Missing parameter: %s.', $item));
            }
        }

        return true;
    }
}
