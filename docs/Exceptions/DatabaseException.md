# DatabaseException

`\WebServCo\Framework\Exceptions\DatabaseException`

Whenever a connection or a query fails in the [PDO database](../Database/PdoDatabase.md) library, this is what you catch.

```php
public function __construct(string $message, ?Throwable $previous = null)
```

---

## Notes

Wrapping a `\PDOException` changes a few things:

- The code becomes the driver error number, for example MySQL's `1062` for a duplicate key.
- The message is the driver message with the codes stripped off.
- `getSqlState()` gives you the SQLSTATE (`23000`).
- For the untouched original message, read `getPrevious()->getMessage()`.

Wrap a `DatabaseException` instead and its code and SQLSTATE are copied over. That lets you rethrow from your own code, which puts your file and line in the log, and still keep the database error intact:

```php
use WebServCo\Framework\Exceptions\DatabaseException;

try {
    return $this->db->getRows('SELECT ...', []);
} catch (DatabaseException $e) {
    // Rethrow in order to pinpoint the query location in the logs.
    throw new DatabaseException($e->getMessage(), $e);
}
```

Catching a duplicate key looks like this:

```php
try {
    $this->db->insert('user', ['email' => $email]);
} catch (DatabaseException $e) {
    if ($e->getCode() !== 1062) {
        throw $e;
    }
    // Email already registered.
    return false;
}
```

Since `1062` and friends aren't HTTP statuses, an uncaught `DatabaseException` always ends on a 500 page.
