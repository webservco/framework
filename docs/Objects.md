# Objects

## From array

```php
class MyClass
{
    public $foo;
    public $bar;

    use \WebServCo\Framework\Traits\FromArrayTrait;
}

$array = [
    'foo' => 'value 1',
    'bar' => 'value2',
];
$object = MyClass::fromArray($array);
```

---

## To array

```php
class MyClass implements \WebServCo\Framework\Interfaces\ArrayInterface
{
    public $foo;
    public $bar;

    use \WebServCo\Framework\Traits\ToArrayTrait;
}

$object = new MyClass();

$array = $object->toArray();
```

---
