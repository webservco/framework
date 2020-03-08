<?php
namespace WebServCo\Framework\DataTables;

use WebServCo\Framework\ArrayObject\Items;

class RequestHelper extends AbstractHelper
{
    public static function init($data, $required = [])
    {
        parent::init($data, ['draw', 'columns', 'order', 'start', 'length', 'search']);

        foreach (['columns', 'order', 'search'] as $item) {
            if (!is_array($data[$item])) {
                throw new \InvalidArgumentException(sprintf('Invalid parameter: %s.', $item));
            }
        }

        $columns = new Items(new ColumnArrayObject());
        foreach ($data['columns'] as $item) {
            $columnItem = new Column(
                isset($item['data']) ? $item['data'] : null,
                isset($item['name']) ? $item['name'] : null,
                isset($item['searchable']) ? $item['searchable'] : null,
                isset($item['orderable']) ? $item['orderable'] : null,
                SearchHelper::init($item['search'])
            );
            $columns->set(null, $columnItem);
        }

        $order = new Items(new OrderArrayObject());
        foreach ($data['order'] as $item) {
            $orderItem = new Order(
                isset($item['column']) ? $item['column'] : null,
                isset($item['dir']) ? $item['dir'] : null
            );
            $order->set(null, $orderItem);
        }

        return new Request(
            $data['draw'],
            $columns,
            $order,
            $data['start'],
            $data['length'],
            SearchHelper::init($data['search'])
        );
    }
}
