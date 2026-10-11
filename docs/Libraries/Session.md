# Session

`\WebServCo\Framework\Libraries\Session`

PHP sessions, with the cookie settings in a config file and [ArrayStorage](../ArrayStorage.md) keys (`'user/name'`) for the data.

- Implements: `Interfaces\SessionInterface`

---

## Instantiation

In controllers, repositories and forms: `$this->session()`. Elsewhere: `\WebServCo\Framework\Helpers\SessionLibraryHelper::library()`.

Standalone:

```php
$session = new \WebServCo\Framework\Libraries\Session($settings);
```

---

## Configuration

`config/Session.php`, where every key is optional and the values shown are the defaults:

```php
return [
    // Session lifetime in seconds: garbage collection, and cache expire (in minutes = expire / 60).
    'expire' => 36000,
    'cookie' => [
        'domain' => '',
        'httponly' => true,
        // Seconds. 14 days.
        'lifetime' => 1209600,
        'path' => '/',
        'samesite' => 'Lax',
        'secure' => true,
    ],
];
```

`config/.env.ini` (optional):

```ini
; Throw a SessionException if the custom storage path can't be set. Default true.
app_strict_custom_path = true
```

---

## Starting the session

Start it in your base controller constructor, and only for HTTP requests:

```php
use WebServCo\Framework\Helpers\PhpHelper;

if (!PhpHelper::isCli()) {
    // With a custom storage directory (recommended).
    $this->session()->start(sprintf('%svar/sessions', $projectPath));
}
```

```php
// With PHP's default storage directory.
$this->session()->start();
```

Calling `start()` from the command line throws a `SessionException`. It also throws when a session is already running or `session_start()` fails.

The cookie is always called `webservco`.

---

## Usage

Apart from `start()`, `destroy()` and `regenerate()`, every method checks for a running session first and throws a `SessionException` if there isn't one.

### Setting a value

```php
$this->session()->set('user/name', 'Aria');
```

### Adding a value to a list

```php
$this->session()->add('user/roles', 'admin');
$this->session()->add('user/roles', 'editor');
// ['admin', 'editor']
```

If the key already holds something, it has to be an array.

### Checking if a value exists

```php
$exists = $this->session()->has('user/name');
```

### Retrieving data

```php
$name = $this->session()->get('user/name');
$name = $this->session()->get('user/name', 'N/A');
```

### Clearing a value

This keeps the key and sets it to `null`, so `has()` still says `true`.

```php
$this->session()->clear('user/name');
```

### Removing a value

This deletes the key. A key that isn't there throws an `ArrayStorageException`, hence the `has()` check.

```php
if ($this->session()->has('user/name')) {
    $this->session()->remove('user/name');
}
```

### Login and logout

```php
// After login: new session id, same data. Prevents session fixation.
$this->session()->regenerate();
$this->session()->set('user/id', $userId);

// Logout: empties the data, expires the cookie and destroys the session.
$this->session()->destroy();
```

---

## Notes

Give the sessions a directory of their own, one that exists and is writable. On a shared server, another application with a shorter lifetime will otherwise run garbage collection on the shared default directory and log your users out early.

`start()` sets `session.gc_probability` to 1. Garbage collection then runs on your requests even on systems (Debian, for one) that disable it and clean up with a cron job.

On logout, `destroy()` expires the cookie using the configured path, domain and flags, then calls `session_destroy()`.
