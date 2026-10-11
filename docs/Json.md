# JSON

`\WebServCo\Framework\Json\Data`, `\WebServCo\Framework\Json\Response`

JSON responses in whatever shape you need. If `{"data": ..., "result": ...}` is fine, [`outputJson()`](Output/Methods.md#outputjsonarray-content-bool-result--true-response) is less typing.

---

## Usage

```php
use WebServCo\Framework\Json\Data;
use WebServCo\Framework\Json\Response;

// \WebServCo\Framework\Interfaces\JsonInterface
$jsonData = new Data(['id' => 12, 'name' => 'Aria']);

// \WebServCo\Framework\Http\Response with Content-Type: application/json
return new Response($jsonData);
// With a status code.
return new Response(new Data(['error' => 'Not found.']), 404);
```

Anything that implements `JsonInterface` can go in, a [DataTables](DataTables.md) response included:

```php
return new Response($dataTables->getResponse($request));
```

### Your own JSON object

```php
use WebServCo\Framework\Interfaces\JsonInterface;

final class Item implements JsonInterface
{
    public function __construct(private int $id, private string $name)
    {
    }

    public function toJson(): string
    {
        return (string) json_encode(['id' => $this->id, 'name' => $this->name]);
    }
}
```

---

## Notes

`Data::toJson()` calls `json_encode()` with no flags. Feed it something it can't encode, like a string with invalid UTF-8, and you get an empty body with no error anywhere.
