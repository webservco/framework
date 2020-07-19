# CSV files

## `\WebServCo\Framework\Files\CsvFile`

## Helper: `\WebServCo\Framework\Exceptions\ApplicationException\CsvCreator`

```php
$csvCreator = new \WebServCo\Framework\Files\CsvCreator(';', '"');
$csvFile = $csvCreator->getCsvFile(
    'test.csv',
    $data,
    true // $addHeader
);
return $csvFile->getDownloadResponse();

```
