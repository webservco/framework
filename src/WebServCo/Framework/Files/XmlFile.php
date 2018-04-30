<?php
namespace WebServCo\Framework\Files;

final class XmlFile extends AbstractFile
{
    public function __construct(string $fileName, string $fileData, $formatOutput = false)
    {
        $domDocument = new \DOMDocument;
        if ($formatOutput) {
            $domDocument->preserveWhiteSpace = false;
            $domDocument->formatOutput = true;
        }
        $domDocument->loadXML($fileData);
        $fileData = $domDocument->saveXML();

        parent::__construct($fileName, $fileData, 'text/xml');
    }
}
