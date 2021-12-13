<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

class SearchHelper extends AbstractHelper
{
    /**
    * @param array<string,mixed> $data
    * @param array<int,string> $required
    */
    public static function init(array $data, array $required = []): Search
    {
        $required = $required; // reserved for future use.

        parent::validate($data, ['value', 'regex']);

        foreach (['value', 'regex'] as $item) {
            if (!isset($data[$item])) {
                throw new \InvalidArgumentException(\sprintf('Missing search parameter: %s.', $item));
            }
        }

        return new Search($data['value'], \filter_var($data['regex'], \FILTER_VALIDATE_BOOLEAN));
    }
}
