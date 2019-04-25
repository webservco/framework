# Abstract Object

Provides access to protected member `$db`.
Use it in any class that needs a database object.

## Example

```php
class MyClass extends \WebServCo\Framework\Database\AbstractObject
{
    public function getitems()
    {
        return $this->db->query(...);
    }
}
```
