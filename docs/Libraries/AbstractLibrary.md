# AbstractLibrary

`\WebServCo\Framework\AbstractLibrary`

Every library, controller, repository and form starts from this class, which holds two arrays:

- **settings**, passed to the constructor. Framework libraries get theirs from `config/<Library>.php`.
- **data**, filled in at runtime.

Keys in both follow the [ArrayStorage](../ArrayStorage.md) syntax (`'index1/a1'`).

- Implements: `Interfaces\ArrayInterface`, `Interfaces\DataInterface`, `Interfaces\LibraryInterface`, `Interfaces\SettingsInterface`

---

## Usage

```php
use WebServCo\Framework\AbstractLibrary;

final class MyLibrary extends AbstractLibrary
{
}

$myLibrary = new MyLibrary([
    'key1' => 'value1',
    'key2' => ['nested' => 'value2'],
]);
```

### Settings

```php
// Set a setting after the initialization.
$myLibrary->setSetting('key3', 'value3');

echo $myLibrary->setting('key1'); // value1
echo $myLibrary->setting('key2/nested'); // value2
// Default value if the key doesn't exist.
echo $myLibrary->setting('noexist', 'foo'); // foo
```

### Data

```php
$myLibrary->setData('index1', ['a1' => 'v1', 'a2' => 'v2']);
$myLibrary->setData('index2', 'data2');
$myLibrary->setData('index3/b1', 'v3');

echo $myLibrary->data('index1/a1'); // v1
// Default value if the key doesn't exist.
echo $myLibrary->data('noexist', 'bar'); // bar
// Default value if the key doesn't exist, or exists but is empty.
echo $myLibrary->dataElse('noexist', 'bar'); // bar

// All data.
$data = $myLibrary->getData();

// Clear all data.
$myLibrary->clearData();
```

### Array

```php
// ['data' => [...], 'settings' => [...]]
$array = $myLibrary->toArray();
```

---

## Notes

Give `setData()` or `setSetting()` an empty key (`''`, `0`, `null`) and nothing is stored. You get `false` back.

You can override the constructor and `toArray()`. Everything else is `final`.
