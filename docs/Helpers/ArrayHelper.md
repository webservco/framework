# ArrayHelper

`\WebServCo\Framework\Helpers\ArrayHelper`

---

## Usage

```php
use WebServCo\Framework\Helpers\ArrayHelper;

$result = ArrayHelper::method($arguments);
```

---

## Static methods

### `get(array $array, mixed $key, mixed $defaultValue = null): mixed`

The value if the key exists, otherwise the default. A key holding `null` counts as existing.

```php
ArrayHelper::get(['a' => 1], 'a');          // 1
ArrayHelper::get(['a' => 1], 'b', 0);       // 0
```

Nested arrays and `'a/b'` keys are [ArrayStorage](../ArrayStorage.md)'s job.

### `isMultidimensional(array $array): bool`

Only the first element is checked. If it's an array you get `true`, and an empty array gives `false`.

```php
ArrayHelper::isMultidimensional([['id' => 1]]);  // true
ArrayHelper::isMultidimensional(['id' => 1]);    // false
```

### `toUrlQueryString(array $array): ?string`

Deprecated in favor of `http_build_query()`.

It builds `?a=1&b=2` with no URL encoding at all, and returns `null` for an empty array.
