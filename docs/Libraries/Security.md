# Security

`\WebServCo\Framework\Libraries\Security`

Old salt and hash helpers built on SHA-1, kept for existing data.

---

## Instantiation

In controllers, repositories and forms: `$this->security()`. Elsewhere: `\WebServCo\Framework\Helpers\SecurityLibraryHelper::library()`.

---

## Usage

```php
// Reversed slice of sha1(strtolower($string)), 20 characters by default.
$salt = $this->security()->getSalt($email);
$salt = $this->security()->getSalt($email, 10);

// $salt . sha1($salt . $string)
$hash = $this->security()->getHash($string, $salt);
```

---

## Notes

Don't use this for new passwords. The salt comes from the input itself, so the same string always gets the same salt, and SHA-1 is fast enough that guessing passwords against it is cheap. Keep it for checking hashes you already have. Anything new should use PHP's `password_hash()` and `password_verify()`.
