# StringHelper

`\WebServCo\Framework\Helpers\StringHelper`

---

## Usage

```php
use WebServCo\Framework\Helpers\StringHelper;

$result = StringHelper::method($arguments);
```

---

## Static methods

### `getContextAsString(mixed $context): string`

Captures `var_dump()` output as a string, handy for arrays and objects. `FileLogger` writes its context files with it.

### `isEmpty(string $string): bool`

Only `''` counts. `empty()` would also say yes to `'0'`, and this method doesn't.

### `linkify(string $string): string`

Wraps every URL in an `<a>` tag. The regular expression is adapted from https://stackoverflow.com/a/507459

```php
StringHelper::linkify('See https://example.com/page');
// See <a href="https://example.com/page">https://example.com/page</a>
```

No escaping happens here. Escape the text first and linkify the result.

### `startsWith(string $haystack, string $needle, bool $ignoreCase = true): bool`

Ignores case unless you pass `false`, and handles multibyte strings when `ext-mbstring` is installed.

```php
StringHelper::startsWith('Hello', 'he');         // true
StringHelper::startsWith('Hello', 'he', false);  // false
```

For a case sensitive check, PHP 8's own `str_starts_with()` is simpler.
