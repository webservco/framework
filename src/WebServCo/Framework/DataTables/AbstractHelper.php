<?php
namespace WebServCo\Framework\DataTables;

abstract class AbstractHelper
{
    public static function init($data, $required = [])
    {
        if (!is_array($data)) {
            throw new \InvalidArgumentException('Data is not an array');
        }

        foreach ($required as $item) {
            if (!isset($data[$item])) {
                throw new \InvalidArgumentException(sprintf('Missing parameter: %s', $item));
            }
        }
    }
}
