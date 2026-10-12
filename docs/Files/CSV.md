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

`getCsvFile()` gives you a file with the `text/csv` content type.

Anything that goes wrong surfaces as a `FileException`.
