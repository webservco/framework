<?php

declare(strict_types=1);

namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\ArrayObject\Items;

class RequestHelper extends AbstractHelper
{

    /**
    * @param array<string,mixed> $data
    * @param array<int,string> $required
    */
    public static function init(array $data, array $required = []): Request
    {
        $required = $required; // reserved for future use.

        parent::validate($data, ['draw', 'columns', 'order', 'start', 'length', 'search']);

        foreach (['columns', 'order', 'search'] as $item) {
            if (!\is_array($data[$item])) {
                throw new \InvalidArgumentException(\sprintf('Invalid parameter: %s.', $item));
            }
        }

        $columns = new Items(new ColumnArrayObject());
        foreach ($data['columns'] as $item) {
            if (!\array_key_exists('data', $item) || !\array_key_exists('name', $item)) {
                //continue;
            }
            $columnItem = new Column(
                $item['data'],
                $item['name'],
                \filter_var($item['searchable'], \FILTER_VALIDATE_BOOLEAN),
                \filter_var($item['orderable'], \FILTER_VALIDATE_BOOLEAN),
                SearchHelper::init($item['search']),
            );
            $columns->set(null, $columnItem);
        }

        $order = new Items(new OrderArrayObject());
        foreach ($data['order'] as $item) {
            if (!\array_key_exists('column', $item) || !\array_key_exists('dir', $item)) {
                continue;
            }
            $orderItem = new Order($item['column'], $item['dir']);
            $order->set(null, $orderItem);
        }

        return new Request(
            (int) $data['draw'],
            $columns,
            $order,
            (int) $data['start'],
            (int) $data['length'],
            SearchHelper::init($data['search']),
        );
    }
}
