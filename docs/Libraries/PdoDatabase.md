# Database (PDO)

## \WebServCo\Framework\Libraries\MysqlPdoDatabase
## \WebServCo\Framework\Libraries\PgsqlPdoDatabase

## Initialization

### Framework

[TODO]

### Standalone initialization

```
$db = new \WebServCo\Framework\Libraries\MysqlPdoDatabase(
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

```php
$this->db()->insert('<tableName>', ['<col1>' => <val1>, '<col2>' => <val2>]);
```

#### INSERT ... ON DUPLICATE KEY UPDATE

> Not supported when adding multiple rows at once

```php
$this->db()->insert('<tableName>', [<addData>], [<updateData>]);
$this->db()->insert(
    '<tableName>',
    ['<col1>' => <val1>, '<col2>' => <val2>],
    ['<col2>' => <val2>]
);
```

#### INSERT IGNORE

```php
$this->db()->insertIgnore('<tableName>', ['<col1>' => <val1>, '<col2>' => <val2>]);
```

#### REPLACE

```php
$this->db()->replace('<tableName>', ['<col1>' => <val1>, '<col2>' => <val2>]);
```

#### Add multiple items at once

> MySQL: Please note the information about the last inserted Id below

```php
$this->db()->insert(
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
return $this->db()->getRows("SELECT <col1>, <col2> FROM <table> WHERE 1", []);
```

#### One row
```php
return $this->db()->getRow("SELECT <col1>, <col2> FROM <table> WHERE <col3> = ?", ['<val3>']);
```

#### One column
```php
return $this->db()->getColumn(
    "SELECT <col1>, <col2> FROM <table> WHERE <col3> = ?", // query
    ['<val3>'], // params
    0 // column number
);
```

### Custom query

```php
$data = [];
$stmt = $this->db()->query("SELECT <col1>, <col2> FROM <table> WHERE <col3> = ?", ['<val3>']);
while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
    $data[] = $row;
}
```

```php
$this->db()->query("DELETE FROM <table> WHERE <col> = ?", [<val>]);
return $this->db()->affectedRows();
```

```php
$this->db()->query("UPDATE <table> SET <col1> = ? WHERE <col2> = ?", ['<val1>', '<val2>']);
return $this->db()->affectedRows();
```

### Transactions

```php
$this->db()->transaction(
    [
        ["TRUNCATE TABLE <table>", []],
        ["INSERT INTO <table> (<col1>, <col2>) VALUES (?, ?)", [<val1>, <val2>]],
        ["INSERT INTO <table> (<col1>, <col2>) VALUES (?, ?)", [<val1>, <val2>]],
        ["INSERT INTO <table> (<col1>, <col2>) VALUES (?, ?)", [<val1>, <val2>]],
    ]
);
return $this->db()->lastInsertId();
```

### Get last inserted Id

> [MySQL](https://dev.mysql.com/doc/refman/5.5/en/information-functions.html#function_last-insert-id):
> If you insert multiple rows using a single INSERT statement, LAST_INSERT_ID() returns the value generated for the first inserted row only. The reason for this is to make it possible to reproduce easily the same INSERT statement against some other server.

```php
return $this->db()->lastInsertId();
```

### Get affected rows

```php
return $this->db()->affectedRows();
```

### Get row count

> It is strongly recommended to avoid this functionality, as it adds an usually unnecessary overhead.
>
> Alternative: use `$this->db()->getRows()` and count the result.

```php
$this->db()->query("SELECT <col> FROM <table>");
return $this->db()->numRows();
```

### Check if value exists
```php
return $this->db()->valueExists('<table>', '<col>', <val>);
```
