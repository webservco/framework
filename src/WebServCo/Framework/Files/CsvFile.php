<?php
namespace WebServCo\Framework\Files;

class CsvFile extends AbstractFile
{
    const CONTENT_TYPE = 'text/csv';

    public function __construct(string $fileName, string $fileData)
    {
        parent::__construct($fileName, $fileData, self::CONTENT_TYPE);
    }
}
