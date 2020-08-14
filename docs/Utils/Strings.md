# Utils - Strings

---

## Usage

```php
$result = \WebServCo\Framework\Utils\Strings::method($arguments);
```

---

## Static methods

### `contains($haystack, $needle, $ignoreCase = true)`

Check if a string contains another string.

### `endsWith($haystack, $needle)`

Check if a string ends with another string.

### `getContextAsString($context)`

Get string representation of variable (usually an array or an object).

### `getSlug($string)`

Create a web friendly URL slug from a string.

Adapted from https://stackoverflow.com/a/13331948

### `linkify($string)`

Convert URL's into links (adds the <a> tag)

Adapted from https://stackoverflow.com/a/507459

### `startsWith($haystack, $needle, $ignoreCase = true)`

Check if a string starts with another string.

### `stripNonDigits($haystack)`

Strip any non digit char.

---
