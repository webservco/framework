# AbstractLibrary

## \WebServCo\Framework\AbstractLibrary

---

## Usage

```php

class MyLibrary extends \WebServCo\Framework\AbstractLibrary
{}
```

```php

$settings = [
    'key1' => 'value1',
    'key2' => 'value2',
];

$data = [
    'index1' => [
        'a1' => 'v1',
        'a2' => 'v2',
    ],
    'index2' => 'data2',
];

$myLibrary = new MyLibrary($settings);
```

---

## Settings methods

```php

// set a setting after the initialization
$myLibrary->setSetting('key3', 'value3');

// access setting by key
echo $myLibrary->setting('key1'); // value1
// choose a default value if key doesn't exist
echo $myLibrary->setting('noexist', 'foo'); // foo
```

---

## Data methods

```php

// clear all data
$myLibrary->clearData();

// set data
$myLibrary->setData('index1', $data['index1']);
$myLibrary->setData('index2', $data['index2']);

// access data by key
echo $myLibrary->data('index1/a1'); // v1
// choose a default value if key doesn't exist
echo $myLibrary->data('noexist', 'bar'); // bar

// get all data
$data = $myLibrary->getData();
```

---

## Other methods

```php

// get an array representation of the library
$array = $myLibrary->toArray(); // ['data'=> [...], 'settings' => [...]]

```
