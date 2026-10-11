# AbstractObject

`\WebServCo\Framework\Database\AbstractObject`

A tiny base class for anything that talks to the database. Its constructor takes a `DatabaseInterface` and keeps it in the protected `$db` property.

---

## Usage

```php
use WebServCo\Framework\Database\AbstractObject;
use WebServCo\Framework\Exceptions\DatabaseException;

final class ItemStorage extends AbstractObject
{
    /**
     * @return array<int,array<string,float|int|string|null>>
     */
    public function getItems(int $userId): array
    {
        try {
            return $this->db->getRows('SELECT id, name FROM item WHERE user_id = ?', [$userId]);
        } catch (DatabaseException $e) {
            // Rethrow in order to pinpoint the query location in the logs.
            throw new DatabaseException($e->getMessage(), $e);
        }
    }
}
```

```php
// In a controller or repository.
$itemStorage = new ItemStorage($this->mysqlPdoDb());
$items = $itemStorage->getItems($userId);
```

The query methods are documented in [PDO](PdoDatabase.md), and the rethrow trick in [DatabaseException](../Exceptions/DatabaseException.md).
