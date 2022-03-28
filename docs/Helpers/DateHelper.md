# DateHelper

---

## Usage

```php
$result = \WebServCo\Framework\Helpers\DateHelper::method($arguments);
```

---

## Static methods

### `format(string $date, string $format = 'Y-m-d')`

Format a date.

Warning: Because of how \strtotime works, an invalid date will be converted to the next valid value.

If you need to validate a date, do not format it beforehand.

### `validate(string $date, string $format = 'Y-m-d')`

Validate an already formatted date.

---
