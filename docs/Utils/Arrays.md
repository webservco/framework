# Utils - Arrays

---

## Usage

```php
$result = \WebServCo\Framework\Utils\Arrays::method($arguments);
```

---

## Static methods

### `has($array, $value)`

Returns true if `$array` is an array and contains `$value`

### `get($array, $key, $defaultValue = false)`

Get a value from an array if it exists, otherwise a specified default value.

### `isMultidimensional($array = [])`

Check if an array is multidimensional

### `nullToEmptyString($array = [])`

Convert all array values that are `NULL`, to an empty string.
Useful for database inserts where values can not be null.

---
