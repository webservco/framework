# Database (PDO)

## `\WebServCo\Framework\Libraries\MysqlPdoDatabase`
## `\WebServCo\Framework\Libraries\PgsqlPdoDatabase`

## Initialization

### Framework

[TODO]

### Standalone initialization

```php
$this->db = new \WebServCo\Framework\Libraries\MysqlPdoDatabase(
    [
        'connection' => [
            'host' => '',
            'username' => '',
            'passwd' => '',
            'dbname' => '',
            'port' => '',
        ],
    ]
);
```

## Usage

### Add

#### INSERT

* This method uses a transaction.

* Returns lastInsertId regardless of queries executed as it's not possible to retrieve lastInsertId after commit

```php
$lastInsertId = $this->db->insert('<tableName>', ['<col1>' => <val1>, '<col2>' => <val2>]);
```


#### INSERT ... ON DUPLICATE KEY UPDATE

* This method uses a transaction.

* Not supported when adding multiple rows at once

```php
$this->db->insert('<tableName>', [<addData>], [<updateData>]);
$this->db->insert(
    '<tableName>',
    ['<col1>' => <val1>, '<col2>' => <val2>],
    ['<col2>' => <val2>]
);
```

#### INSERT IGNORE

* This method uses a transaction.

* Returns lastInsertId regardless of queries executed as it's not possible to retrieve lastInsertId after commit

```php
$lastInsertId = $this->db->insertIgnore('<tableName>', ['<col1>' => <val1>, '<col2>' => <val2>]);
```

#### REPLACE

* This method uses a transaction.

* Returns lastInsertId regardless of queries executed as it's not possible to retrieve lastInsertId after commit

```php
$lastInsertId = $this->db->replace('<tableName>', ['<col1>' => <val1>, '<col2>' => <val2>]);
```

#### Add multiple items at once

* MySQL: Please note the information about the last inserted Id below

```php
$this->db->insert(
    '<tableName>',
    [
        ['<col1>' => <val1>, '<col2>' => <val2>],
        ['<col1>' => <val3>, '<col2>' => <val4>],
    ]
);
```

### Retrieve data

#### Multiple rows

```php
return $this->db->getRows("SELECT <col1>, <col2> FROM <table> WHERE 1", []);
```

#### One row
```php
return $this->db->getRow("SELECT <col1>, <col2> FROM <table> WHERE <col3> = ?", ['<val3>']);
```

#### One column
```php
return $this->db->getColumn(
    "SELECT <col1>, <col2> FROM <table> WHERE <col3> = ?", // query
    ['<val3>'], // params
    0 // column number
);
```

### Custom query

```php
$data = [];
$stmt = $this->db->query("SELECT <col1>, <col2> FROM <table> WHERE <col3> = ?", ['<val3>']);
while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $data[] = $row;
}
```

```php
$this->db->query("DELETE FROM <table> WHERE <col> = ? LIMIT 1", [<val>]);
return $this->db->affectedRows();
```

```php
$this->db->query("UPDATE <table> SET <col1> = ? WHERE <col2> = ?", ['<val1>', '<val2>']);
return $this->db->affectedRows();
```

### Transactions

```php
$this->db->transaction(
    [
        ["INSERT INTO <mainTable> (<col1>, <col2>) VALUES (?, ?)", [<val1>, <val2>]],
        ["SET @id = LAST_INSERT_ID();", []],
        ["INSERT INTO <secondaryTable> (id, <col1>, <col2>) VALUES (@id, ?, ?)", [<val1>, <val2>]],
        ["INSERT INTO <secondaryTable> (id, <col1>, <col2>) VALUES (@id, ?, ?)", [<val1>, <val2>]],
    ]
);
return $this->db->getColumn("SELECT @id", [], 0);
```

### Get last inserted Id

* [MySQL](https://dev.mysql.com/doc/refman/5.5/en/information-functions.html#function_last-insert-id):
> If you insert multiple rows using a single INSERT statement, LAST_INSERT_ID() returns the value generated for the first inserted row only. The reason for this is to make it possible to reproduce easily the same INSERT statement against some other server.

* **Important**: When using transactions, this method is not useful as it returns `0`. If using the insert helpers, `lastInsertId` is returned by those methods.

```php
return $this->db->lastInsertId();
```

### Get affected rows

```php
return $this->db->affectedRows();
```

### Get row count

*  It is strongly recommended to avoid this functionality, as it adds an usually unnecessary overhead.
* Alternative: use `$this->db->getRows()` and count the result.

```php
$this->db->query("SELECT <col> FROM <table>");
return $this->db->numRows();
```

### Check if value exists
```php
return $this->db->valueExists('<table>', '<col>', '<val>');
```

### Check if table exists
```php
return $this->db->tableExists('<table>');
```

---

## Error Handling

Please see [DatabaseException](/docs/Exceptions/DatabaseException.md).

---

## Unbuffered query example

```php
$db->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
$stmt = $db->query(...);
$db->setAttribute(\PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
while (...)
```

---

## \WebServCo\Framework\Database\Utils\PreparedStatements

### `generatePlaceholdersString(array $data = []): string`

Generate placeholders string from array data.

Eg. if array has 3 items, it generates the string "?, ?, ?".
