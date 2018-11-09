<?php
namespace WebServCo\Framework\Files;

class XmlFile extends AbstractFile
{
    const CONTENT_TYPE = 'text/xml';

    public function __construct(string $fileName, string $fileData, $formatOutput = false)
    {
        $domDocument = new \DOMDocument;
        $domDocument->preserveWhiteSpace = false;
        if ($formatOutput) {
            $domDocument->formatOutput = true;
        }
        $domDocument->loadXML($fileData);
        $fileData = $domDocument->saveXML();

        parent::__construct($fileName, $fileData, self::CONTENT_TYPE);
    }
}
