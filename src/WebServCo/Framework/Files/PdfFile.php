<?php
namespace WebServCo\Framework\Files;

final class PdfFile extends AbstractFile
{
    public function __construct(string $fileName, string $fileData)
    {
        parent::__construct($fileName, $fileData, 'application/pdf');
    }
}
