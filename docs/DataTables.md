# DataTables

Namespace `\WebServCo\Framework\DataTables`

Server side processing for the [DataTables](https://datatables.net/) JavaScript library. The classes here parse what DataTables sends, run your query with column search, ordering and paging applied, and return the JSON it expects.

[webservco/webapp-skeleton](https://github.com/webservco/webapp-skeleton) has a finished frontend to copy from, with Bootstrap styling, expandable row details, saved state with a reset button, a progress indicator and a search box per column.

---

## Usage

### Database implementation (MySQL)

All you write is the query, in a subclass of `AbstractDataTablesDatabase`:

```php
use WebServCo\Framework\DataTables\AbstractDataTablesDatabase;

final class ItemDataTables extends AbstractDataTablesDatabase
{
    protected function getQuery(string $searchPart, string $orderPart, string $limitPart): string
    {
        return sprintf(
            'SELECT SQL_CALC_FOUND_ROWS id, name, status
            FROM item
            WHERE 1
            %s
            %s
            %s',
            $searchPart,
            $orderPart,
            $limitPart,
        );
    }

    protected function getRecordsTotalQuery(): string
    {
        return 'SELECT COUNT(*) FROM item';
    }
}
```

- Keep `SQL_CALC_FOUND_ROWS`. The filtered count comes from `SELECT FOUND_ROWS()` afterwards.
- The search part begins with `AND`, so there has to be a `WHERE` before it, even if it's just `WHERE 1`.
- Whatever DataTables sends as `columns[i][data]` must match a result column name. It gets escaped as one identifier, so `table.column` won't work. Use plain names, or aliases in an outer query.

### Controller

```php
use WebServCo\Framework\DataTables\RequestHelper;
use WebServCo\Framework\Interfaces\ResponseInterface;
use WebServCo\Framework\Json\Response;

public function items(): ResponseInterface
{
    // \WebServCo\Framework\DataTables\Request. Throws \InvalidArgumentException for an invalid request.
    $request = RequestHelper::init($this->request()->getData());

    $dataTables = new ItemDataTables($this->mysqlPdoDb());

    // \WebServCo\Framework\DataTables\Response, a JsonInterface
    return new Response($dataTables->getResponse($request));
}
```

### Frontend

Send the request as a POST, because the controller reads `$this->request()->getData()`:

```js
new DataTable('#items', {
    serverSide: true,
    ajax: {url: '/Item/items', type: 'POST'},
    columns: [
        {data: 'id'},
        {data: 'name'},
        {data: 'status', orderable: false},
    ],
});
```

---

## Custom data

Data that doesn't come from MySQL (an API, a file) goes through `AbstractDataTables`, with three methods to fill in:

```php
use WebServCo\Framework\DataTables\AbstractDataTables;
use WebServCo\Framework\DataTables\Request;

final class ApiDataTables extends AbstractDataTables
{
    protected function getData(Request $request): array
    {
        // $request->getStart(), $request->getLength(), $request->getColumns(), $request->getOrder()
        return [['id' => 1, 'name' => 'a']];
    }

    protected function getRecordsFiltered(): int
    {
        return 1;
    }

    protected function getRecordsTotal(): int
    {
        return 1;
    }
}
```

Need a row id (`DT_RowId`) or formatted values in the database version? Override `getData(ArrayObjectInterface $columnArrayObject, PDOStatement $pdoStatement): array`.

---

## Notes

Searching works per column only. Every searchable column with a value adds `AND <column> LIKE ?` with `%<value>%` bound to it. The global search box (`search[value]`) does nothing, and neither does the regex option.

`RequestHelper::init()` insists on `draw`, `columns`, `order`, `start`, `length` and `search`. Watch the `order` one. A form POST drops empty arrays, so a table configured with `order: []` sends no `order` at all and the request fails, which is why you should always keep a default order.

An unknown direction falls back to `ASC`, and columns marked `orderable: false` are left out of the `ORDER BY`.

Picking "All" in the length menu (`length = -1`) drops the `LIMIT`.

Without a search, the total count query is skipped and the filtered count doubles as the total.

The JSON looks like `{"data": [...], "draw": n, "recordsFiltered": n, "recordsTotal": n}`.
