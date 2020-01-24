# Objects

## From array

```php
class MyClass extends \WebServCo\Framework\AbstractClassFromArray
{
    public $foo;
    public $bar;
}

$array = [
    'foo' => 'value 1',
    'bar' => 'value2',
];
$object = new MyClass($array);
```

---

## To array

```php
class MyClass
{
    public $foo;
    public $bar;

    use \WebServCo\Framework\Traits\ToArrayTrait;
}

$object = new MyClass();

$array = $object->toArray();
```

---
