# Arrays

## `toArray`

* `\WebServCo\Framework\Interfaces\ArrayInterface`
* `\WebServCo\Framework\Traits\ToArrayTrait`
    * This works only for public methods.

### Example

```php
<?php

class MyClass implements \WebServCo\Framework\Interfaces\ArrayInterface
{
    public $param;

    use \WebServCo\Framework\Traits\ToArrayTrait;

    public function __construct($param)
    {
        $this->param = $param;
    }
}

$class = new MyClass('foo');
$array = $class->toArray();
print_r($array); // Array ( [param] => foo )
```

---
