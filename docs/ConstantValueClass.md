# Constant Value Class

Based on the article ["Expressive, type-checked constants for PHP"](https://www.webfactory.de/blog/expressive-type-checked-constants-for-php).

## Example

```php
# Constant Value Class

class Type implements \WebServCo\Framework\Interfaces\ConstantValueClassInterface
{
    private const EXPORT = 1;
    private const IMPORT = 2;

    use \WebServCo\Framework\Traits\ConstantValueClassTrait;

    public static function export(): self
    {
        return self::constant(self::EXPORT);
    }

    public static function import(): self
    {
        return self::constant(self::IMPORT);
    }
}

# Usage

class Shipment
{
    public function send(Type $type) : bool
    {
        // ...
    }
}
$shipment = new Shipment();
$shipment->send(Type::import());

// or
$type = 2; // request parameter, user input, etc
$shipment->send(Type::fromValue($type));

// get value (eg. for form select)
echo Type::import()->value(); // outputs "1"
```

---
