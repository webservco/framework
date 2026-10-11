# Cookie

`\WebServCo\Framework\Libraries\Cookie`

Setting, reading and removing cookies.

---

## Instantiation

In controllers, repositories and forms: `$this->cookie()`. Elsewhere: `\WebServCo\Framework\Helpers\CookieLibraryHelper::library()`.

It loads `config/Cookie.php` if the file exists, though nothing in the framework reads from it. Outside the framework, create it yourself:

```php
$cookie = new \WebServCo\Framework\Libraries\Cookie();
```

---

## Usage

### Setting a value

```php
$this->cookie()->set(
    // name
    'username',
    // value
    'Aria',
    // expires: Unix timestamp, 0 = until the browser closes
    time() + 60 * 60 * 24 * 30,
    // path
    '/',
    // domain
    '',
    // secure
    true,
    // httponly
    true,
    // samesite: 'Lax', 'Strict' or 'None'
    'Lax',
);

// Defaults: expires 0, path '', domain '', secure true, httponly false, samesite 'Lax'.
$this->cookie()->set('username', 'Aria');
```

The return value is whatever `setcookie()` returned. A `samesite` other than the three allowed values throws an `\InvalidArgumentException`.

### Retrieving a value

```php
// '' if not set.
$username = $this->cookie()->get('username');
$username = $this->cookie()->get('username', 'guest');
```

### Removing a value

```php
// false if the cookie isn't set.
$this->cookie()->remove('username');
```

This unsets it in `$_COOKIE` and sends the cookie again, already expired.

---

## Notes

`set()` runs the value through [RequestHelper::sanitizeString()](../Helpers/RequestHelper.md#sanitizestring) first. Tags, backticks, backslashes, `$`, tabs and new lines disappear and quotes get encoded, so a value that needs any of those should be `base64_encode()`d before you store it.

Reading is a different story. `get()` hands back `$_COOKIE` exactly as the browser sent it, which makes it user input like any other.

`remove()` only knows the default path and domain. For a cookie you set with a custom path or domain, expire it yourself with `set($name, '', 1, $path, $domain)`.
