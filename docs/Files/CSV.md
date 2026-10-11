# CSV files

`\WebServCo\Framework\Files\CsvCreator`, `\WebServCo\Framework\Files\CsvFile`

Turns an array of rows into CSV, either as a string or as a [file](Files.md) you can send straight to the browser.

---

## Usage

```php
use WebServCo\Framework\Files\CsvCreator;

$csvCreator = new CsvCreator(
    // delimiter, default ","
    ';',
    // enclosure, default '"'
    '"',
    // escape, default "" (RFC 4180)
    '',
);

$data = [
    ['id' => 1, 'name' => 'Aria'],
    ['id' => 2, 'name' => 'Radu'],
];

// \WebServCo\Framework\Files\CsvFile
$csvFile = $csvCreator->getCsvFile(
    // fileName
    'users.csv',
    // data
    $data,
    // addHeader: the keys of the first row as the header line
    true,
);

return $csvFile->getDownloadResponse();
```

When all you need is the text:

```php
$csv = $csvCreator->getCsvData($data, true);
```

---

## Notes

A UTF-8 byte order mark goes first. Without it, Excel guesses the encoding and mangles accented characters.

The first 5 MB are built in memory, and anything bigger spills into a temporary file.

An empty array gives you an empty string, with no BOM and no header row.

`getCsvFile()` ends up with `application/octet-stream` as the content type instead of `text/csv` ([Files](Files.md#notes) explains why). Downloads don't care. When you do want `text/csv`, build the file yourself:

```php
use WebServCo\Framework\Files\CsvFile;

$csvFile = new CsvFile('users.csv', $csvCreator->getCsvData($data), CsvFile::CONTENT_TYPE);
```

Anything that goes wrong surfaces as a `FileException`.
