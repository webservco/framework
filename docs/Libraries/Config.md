# Config

`\WebServCo\Framework\Libraries\Config`

A key/value store for your own values at runtime. It also loads the library settings files for the other libraries.

- Implements: `Interfaces\ConfigInterface`
- Related: [Configuration](../Configuration.md) (environment settings and library settings files)

---

## Instantiation

In controllers, repositories and forms: `$this->config()`. Elsewhere: `\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()`.

---

## Usage

```php
// Set a value. Nested keys use the ArrayStorage syntax.
$this->config()->set('app/name', 'My App');
$this->config()->set(['app', 'version'], '1.0');

// Get a value, with a default.
$name = $this->config()->get('app/name', '');

// Merge data under a top level key (recursive, existing values are overwritten).
$this->config()->add('app', ['author' => 'Aria']);
```

### Loading a settings file

```php
use WebServCo\Framework\Environment\Config;

// Includes <projectPath>config/Mail.php. Returns [] if the file is missing or doesn't return an array.
$settings = $this->config()->load('Mail', Config::string('APP_PATH_PROJECT'));
$this->config()->add('mail', $settings);
```

`ConfigLibraryHelper::getSettings('Mail')` is a shortcut for the same thing with `APP_PATH_PROJECT`, and it's how each library helper gets its settings.

---

## Notes

The store starts out empty. Each settings file goes to its own library and never lands here, so `$this->config()->get('Router')` is `null` and the routes are in `$this->router()->setting(...)`.

Because Config is also a [library](AbstractLibrary.md), it has `data()` and `setting()` as well. Those are separate arrays from the store behind `get()`, `set()` and `add()`.
