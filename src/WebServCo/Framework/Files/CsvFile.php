<?php

namespace WebServCo\Framework\Files;

class CsvFile extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{
    public const CONTENT_TYPE = 'text/csv';

    public function __construct($fileName, $fileData)
    {
        parent::__construct($fileName, (string) $fileData, self::CONTENT_TYPE);
    }
}
