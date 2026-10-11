# Objects

Objects from arrays, arrays from objects, and a few small classes that don't fit anywhere else.

---

## From array

`\WebServCo\Framework\Traits\FromArrayTrait`

```php
use WebServCo\Framework\Traits\FromArrayTrait;

final class Item
{
    use FromArrayTrait;

    public ?int $id = null;
    public string $name = '';
}

$item = Item::fromArray([
    'id' => 12,
    'name' => 'Aria',
    // Ignored: not a property.
    'other' => 'x',
]);
```

`fromArray()` calls `new static()`, so a constructor with required parameters breaks it. Missing keys leave the property at its default. Private and protected properties get filled too. Nothing is converted, though, and a `'12'` string headed for an `int` property throws a `TypeError`.

---

## To array

`\WebServCo\Framework\Traits\ToArrayTrait`

Only public properties make it into the array.

```php
use WebServCo\Framework\Interfaces\ArrayInterface;
use WebServCo\Framework\Traits\ToArrayTrait;

final class Item implements ArrayInterface
{
    use ToArrayTrait;

    public int $id = 12;
    public string $name = 'Aria';
}

// ['id' => 12, 'name' => 'Aria']
$array = (new Item())->toArray();
```

The conversion is a `json_encode()` and `json_decode()` round trip. Nested objects become arrays along the way, and a `JsonSerializable` object is converted by its own `jsonSerialize()`.

---

## Validated class

`\WebServCo\Framework\Arrays\AbstractValidatedClass`

The constructor fills properties the way `fromArray()` does, then checks that none of the required ones ended up empty.

```php
use WebServCo\Framework\Arrays\AbstractValidatedClass;

final class Address extends AbstractValidatedClass
{
    public string $street = '';
    public string $city = '';
    public string $zip = '';

    /**
     * @return array<int,string>
     */
    protected function getRequiredProperties(): array
    {
        return ['street', 'city'];
    }
}

// Throws a RequiredArgumentException: "Empty required parameter: city".
$address = new Address(['street' => 'Main 1']);
```

"Empty" is PHP's `empty()`, which means a perfectly valid `0` or `'0'` fails the check as well.

---

## Route

`\WebServCo\Framework\Objects\Route`

What the [Router](Libraries/Router.md) returns, with three public properties, `$class`, `$method` and `$arguments`.

---

## User

`\WebServCo\Framework\AbstractUser`

A starting point for your own user class. It's a [library](Libraries/AbstractLibrary.md) with three error constants for login code, `ERR_DISABLED`, `ERR_LOGIN` and `ERR_NOT_FOUND`.

---

## ArrayObject\Items

`\WebServCo\Framework\ArrayObject\Items`

A thin wrapper around an `ArrayObjectInterface` (in practice an `\ArrayObject`) with `set(int|string|null $index, mixed $item)` and `remove(int $index)`. Removing an item reindexes the list. [DataTables](DataTables.md) is the only user so far.

---

## SourceCode

`\WebServCo\Framework\SourceCode`

Colors XML tags so you can show a document on an HTML page.

```php
use WebServCo\Framework\SourceCode;

$sourceCode = new SourceCode(SourceCode::TYPE_XML, $xml);
echo '<pre>' . $sourceCode->highlight() . '</pre>';
```

So far XML is the only type, and anything else throws a `NotImplementedException`.
