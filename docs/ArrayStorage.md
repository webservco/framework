# ArrayStorage

`\WebServCo\Framework\ArrayStorage`

Static helpers that read and write nested arrays through a path key like `'user/address/city'`. Library data and settings, the session, the Config library, form errors and the request query are all stored this way, which is why the same key syntax works in every one of them.

---

## Keys

A key can be:

- a simple string or int: `'name'`, `0`
- a path, with `/` as the divider (`Settings::DIVIDER`): `'user/address/city'`
- an array of path parts: `['user', 'address', 'city']`

```php
$this->setData('user/address/city', 'Bucharest');
// ['user' => ['address' => ['city' => 'Bucharest']]]
$this->data('user/address/city');
```

---

## Usage

Nothing is changed in place. Every write method returns a new array, so assign the result.

```php
use WebServCo\Framework\ArrayStorage;

$storage = [];

// Set a value. Creates missing levels.
$storage = ArrayStorage::set($storage, 'user/name', 'Aria');

// Get a value, or the default if the key doesn't exist.
$name = ArrayStorage::get($storage, 'user/name', 'N/A');

// Get a value, or the default if the key doesn't exist or the value is empty.
$city = ArrayStorage::getElse($storage, 'user/city', 'N/A');

// Check if a key exists (also when the value is null).
$exists = ArrayStorage::has($storage, 'user/name');

// Append to a list. The existing value must be an array.
$storage = ArrayStorage::add($storage, 'user/roles', 'admin');
$storage = ArrayStorage::add($storage, 'user/roles', 'editor');
// ['user' => ['name' => 'Aria', 'roles' => ['admin', 'editor']]]

// Remove a key. Throws if it doesn't exist.
$storage = ArrayStorage::remove($storage, 'user/roles');

// Merge an array in, recursively. Existing values are overwritten.
$storage = ArrayStorage::append($storage, ['user' => ['email' => 'aria@example.com']]);
```

---

## Notes

An empty key makes `set()`, `add()` and `remove()` throw an `ArrayStorageException`. So does removing a key that isn't there, so call `has()` first when you aren't sure.

Watch out for `false` in nested data. Read through a path key, a stored `false` comes back as the default, so `get(['a' => ['b' => false]], 'a/b', 'x')` gives you `'x'`. With a simple key you get the `false`. Storing `0` or `null` avoids the surprise.

`get()` also falls back to the default for a `null` or `bool` key, and for storage that's empty or not an array.

Flat arrays don't need any of this. [ArrayHelper](Helpers/ArrayHelper.md)`::get()` is enough.
