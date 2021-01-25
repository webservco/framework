# Abstract Object

Provides access to protected member `$db`.
Use it in any class that needs a database object.

## Example

```php
use WebServCo\Framework\Exceptions\DatabaseException;

class MyClass extends \WebServCo\Framework\Database\AbstractObject
{
    public function getitems()
    {
        try {
            return $this->db->query(...);
        } catch (DatabaseException $e) {
            // rethrow in order to pinpoint the query location in the logs.
            throw new DatabaseException($e->getMessage(), $e);
        }
    }
}
```
