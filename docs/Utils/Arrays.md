# Utils - Arrays

---

## Usage

```php
$result = \WebServCo\Framework\Utils\Arrays::method($arguments);
```

---

## Static methods

### `removeEmptyValues($array)`

Removes empty values from input array (recursive).

### `has($array, $value)`

Returns true if `$array` is an array and contains `$value`.

### `get($array, $key, $defaultValue = false)`

Get a value from an array if it exists, otherwise a specified default value.
For multi dimensional arrays please see ArrayStorage.

### `isMultidimensional($array = [])`

Check if an array is multidimensional.

### `nullToEmptyString($array = [])`

Convert all array values that are `NULL`, to an empty string.
Useful for database inserts where values can not be null.

---
