<?php
namespace WebServCo\Framework\Files;

class PdfFile extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{
    const CONTENT_TYPE = 'application/pdf';

    public function __construct($fileName, $fileData)
    {
        parent::__construct($fileName, $fileData, self::CONTENT_TYPE);
    }
}
