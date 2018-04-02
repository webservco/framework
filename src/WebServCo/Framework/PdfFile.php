<?php
namespace WebServCo\Framework;

final class PdfFile extends AbstractFile
{
    public function __construct(string $fileName, string $fileData)
    {
        parent::__construct($fileName, $fileData, 'application/pdf');
    }
}
