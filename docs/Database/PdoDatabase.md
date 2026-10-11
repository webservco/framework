# Database (PDO)

`\WebServCo\Framework\Libraries\MysqlPdoDatabase`, `\WebServCo\Framework\Libraries\PgsqlPdoDatabase`

PDO plus a handful of helpers for prepared queries, fetching rows, inserts and transactions.

- Implements: `Interfaces\DatabaseInterface`
- Requires: `ext-pdo` with the `pdo_mysql` or `pdo_pgsql` driver

---

## Configuration

Connection details are [environment settings](../Configuration.md) in `config/.env.ini`:

```ini
[Database]

app_dbms_host = db
app_dbms_port = 3306
app_dbms_dbname = db
app_dbms_username = db
app_dbms_passwd = "db"
```

The port has to be a number and everything else a string, so put quotes around a password made only of digits.

MySQL connects with `charset=utf8mb4`. For both drivers, errors become exceptions, rows come back as associative arrays, prepared statements are real (not emulated), and connections aren't persistent.

---

## Initialization

### Framework

Controllers, repositories and forms get MySQL with one call:

```php
// \WebServCo\Framework\Interfaces\DatabaseInterface
$db = $this->mysqlPdoDb();
```

Anywhere else, `\WebServCo\Framework\Helpers\MysqlPdoDatabaseLibraryHelper::library()` does the same. The first call opens the connection and later calls share it.

### Standalone

```php
use WebServCo\Framework\Libraries\MysqlPdoDatabase;
use WebServCo\Framework\Libraries\PgsqlPdoDatabase;

// Connects immediately, with the APP_DBMS_* settings.
$db = new MysqlPdoDatabase();
$db = new PgsqlPdoDatabase();
```

If the connection fails you get a [DatabaseException](../Exceptions/DatabaseException.md).

---

## Usage

Placeholders are positional `?` marks. Named ones (`:id`) aren't supported.

### Retrieve data

#### Multiple rows

```php
// [['id' => 1, 'name' => 'a'], ...] or []
$rows = $db->getRows('SELECT id, name FROM item WHERE user_id = ?', [$userId]);
```

#### One row

```php
// ['id' => 1, 'name' => 'a'] or []
$row = $db->getRow('SELECT id, name FROM item WHERE id = ?', [$id]);
```

#### One value

```php
$name = $db->getColumn(
    // query
    'SELECT id, name FROM item WHERE id = ?',
    // params
    [$id],
    // column number, default 0
    1,
);
```

No row means `false`.

### Custom query

```php
// \PDOStatement
$stmt = $db->query('SELECT id, name FROM item WHERE status = ?', ['active']);
while ($row = $stmt->fetch()) {
    // ...
}
```

```php
$db->query('UPDATE item SET name = ? WHERE id = ?', [$name, $id]);
$affected = $db->affectedRows();

$db->query('DELETE FROM item WHERE id = ? LIMIT 1', [$id]);
$affected = $db->affectedRows();
```

#### `IN (...)`

```php
use WebServCo\Framework\Database\Utils\PreparedStatements;

// "?,?,?"
$placeholders = PreparedStatements::generatePlaceholdersString($ids);
$rows = $db->getRows(sprintf('SELECT id FROM item WHERE id IN (%s)', $placeholders), $ids);
```

### Add data

Each insert helper wraps its query in a transaction and returns the last insert id. They only work on MySQL, for reasons explained in the [Notes](#notes).

#### INSERT

```php
$id = $db->insert('item', ['name' => 'a', 'status' => 'active']);
```

#### INSERT ... ON DUPLICATE KEY UPDATE

```php
$db->insert(
    'item',
    // add data
    ['code' => 'A1', 'name' => 'a'],
    // update data
    ['name' => 'a'],
);
```

With several rows at once, the update data is silently ignored.

#### INSERT IGNORE

```php
$id = $db->insertIgnore('item', ['code' => 'A1', 'name' => 'a']);
```

#### REPLACE

```php
$id = $db->replace('item', ['code' => 'A1', 'name' => 'a']);
```

#### Multiple rows at once

```php
$db->insert(
    'item',
    [
        ['code' => 'A1', 'name' => 'a'],
        ['code' => 'A2', 'name' => 'b'],
    ],
);
```

Give every row the same keys in the same order. The column list is built from all the rows combined, yet each row's values are bound in that row's own order, so a row with an extra or missing key shifts everything after it.

Table names may include the database, as in `'other_db.item'`.

### Transactions

```php
$lastInsertId = $db->transaction([
    // [query, params]
    ['INSERT INTO invoice (client_id, total) VALUES (?, ?)', [$clientId, $total]],
    ['SET @id = LAST_INSERT_ID()', []],
    ['INSERT INTO invoice_item (invoice_id, product_id) VALUES (@id, ?)', [$productId1]],
    ['INSERT INTO invoice_item (invoice_id, product_id) VALUES (@id, ?)', [$productId2]],
]);
$invoiceId = $db->getColumn('SELECT @id', [], 0);
```

One failing query rolls back the whole list and throws a `DatabaseException`. On success you get the last insert id, read just before the commit.

### Last insert id

```php
$id = $db->lastInsertId();
// PostgreSQL: the sequence name.
$id = $db->lastInsertId('item_id_seq');
```

For multi-row inserts, keep in mind what the [MySQL documentation](https://dev.mysql.com/doc/refman/8.0/en/information-functions.html#function_last-insert-id) says:

> If you insert multiple rows using a single INSERT statement, LAST_INSERT_ID() returns the value generated for the first inserted row only.

Once a transaction has committed, `lastInsertId()` returns `0`. That's why the insert helpers and `transaction()` hand you the id directly.

### Row count

```php
$db->query('SELECT id FROM item');
$count = $db->numRows();
```

Better to skip this one. `rowCount()` after a `SELECT` isn't reliable on every driver, and `SELECT COUNT(*)` or `count($db->getRows(...))` gets you the number without guessing.

### Checks

```php
// SELECT 1 FROM `item` WHERE `code` = ? LIMIT 1
$exists = $db->valueExists('item', 'code', 'A1');

// true if "SELECT 1 FROM `item` LIMIT 1" doesn't fail
$exists = $db->tableExists('item');
```

### Escaping

```php
// `name` (MySQL), "name" (PostgreSQL)
$column = $db->escapeIdentifier($name);
// `db`.`item` (MySQL)
$table = $db->escapeTableName('db.item');
// Quoted string literal, with PDO::quote(). Prefer parameters.
$value = $db->escape($value);
```

### PDO

```php
// \PDO
$pdo = $db->getPdo();
$db->setAttribute(\PDO::ATTR_TIMEOUT, 5);
```

#### Unbuffered query (MySQL)

A query returning millions of rows can exhaust memory before the first `fetch()`. Turning off buffering streams the rows instead:

```php
$db->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
$stmt = $db->query('SELECT ...', []);
while ($row = $stmt->fetch()) {
    // ...
}
$db->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
```

Until the last row is fetched, the connection can't run anything else.

---

## Error handling

Errors from any method arrive as a [DatabaseException](../Exceptions/DatabaseException.md) whose code is the driver error number.

---

## Notes

On PostgreSQL, `PgsqlPdoDatabase::escapeTableName()` throws a `NotImplementedException`, which takes `insert()`, `insertIgnore()`, `replace()`, `valueExists()` and `tableExists()` down with it. `query()`, `getRows()`, `getRow()`, `getColumn()` and `transaction()` work fine on both. (`INSERT IGNORE`, `REPLACE` and `ON DUPLICATE KEY UPDATE` are MySQL syntax in any case.)

Every parameter is bound as `PDO::PARAM_STR`, and MySQL converts it to the column type. That holds for `LIMIT ?, ?` too.

Arrays aren't allowed as parameters and throw a `DatabaseException`. The one exception is a single level of nesting, which gets flattened, so `[[1, 2], 3]` binds `1, 2, 3`.

There's no way to point these classes at a second database. They always read the `APP_DBMS_*` settings and both are `final`, so for another server or other credentials you'll need plain PDO.
