<?php

namespace WebServCo\Framework\DataTables;

class SearchHelper extends AbstractHelper
{
    public static function init($data, $required = [])
    {
        parent::init($data, ['value', 'regex']);

        foreach (['value', 'regex'] as $item) {
            if (!isset($data[$item])) {
                throw new \InvalidArgumentException(sprintf('Missing search parameter: %s.', $item));
            }
        }

        return new Search($data['value'], $data['regex']);
    }
}
