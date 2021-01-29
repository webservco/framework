<?php
namespace WebServCo\Framework\DataTables;

class SearchHelper extends AbstractHelper
{
    /**
    * @param array<string,mixed> $data
    * @param array<int,string> $required
    * @return Search
    */
    public static function init(array $data, array $required = []): Search
    {
        parent::validate($data, ['value', 'regex']);

        foreach (['value', 'regex'] as $item) {
            if (!isset($data[$item])) {
                throw new \InvalidArgumentException(sprintf('Missing search parameter: %s.', $item));
            }
        }

        return new Search($data['value'], $data['regex']);
    }
}
