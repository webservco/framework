<?php
namespace WebServCo\Framework;

final class CsvFile extends AbstractFile
{
    public function __construct(string $fileName, string $fileData)
    {
        parent::__construct($fileName, $fileData, 'text/csv');
    }
}
