<?php
namespace WebServCo\Framework\Files;

class PdfFile extends AbstractFile
{
    const CONTENT_TYPE = 'application/pdf';

    public function __construct(string $fileName, string $fileData)
    {
        parent::__construct($fileName, $fileData, self::CONTENT_TYPE);
    }
}
