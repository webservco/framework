<?php declare(strict_types = 1);

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
            $columnItem = new Column(
                $item['data'] ?? null,
                $item['name'] ?? null,
                $item['searchable'] ?? null,
                $item['orderable'] ?? null,
                SearchHelper::init($item['search'])
            );
            $columns->set(null, $columnItem);
        }

        $order = new Items(new OrderArrayObject());
        foreach ($data['order'] as $item) {
            $orderItem = new Order($item['column'] ?? null, $item['dir'] ?? null);
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
