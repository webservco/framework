# DateHelper

`\WebServCo\Framework\Helpers\DateHelper`

---

## Usage

```php
use WebServCo\Framework\Helpers\DateHelper;

$result = DateHelper::method($arguments);
```

---

## Static methods

### `format(string $date, string $format = 'Y-m-d'): string`

Formats any date `strtotime()` can read.

```php
DateHelper::format('9 October 2024');            // 2024-10-09
DateHelper::format('2024-10-09 14:30', 'd.m.Y'); // 09.10.2024
```

Careful with user input. `strtotime()` rolls an impossible date over to a real one, so `2024-02-30` comes out as `2024-03-01`, and complete garbage becomes the Unix epoch (`1970-01-01` in UTC). Formatting before validating hides the mistake, so validate the raw value and format afterwards.

### `validate(string $date, string $format = 'Y-m-d'): bool`

Checks that a date is real and written in exactly the given format. It returns `true` or throws a `DateTimeException`, never `false`.

```php
use WebServCo\Framework\Exceptions\DateTimeException;

try {
    DateHelper::validate($this->request()->data('date', ''));
} catch (DateTimeException $e) {
    // "Invalid date or format." or "Invalid date."
}

DateHelper::validate('09.10.2024', 'd.m.Y'); // true
DateHelper::validate('2024-02-30');          // throws: Invalid date.
```
