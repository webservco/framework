<?php

namespace WebServCo\Framework\Files;

class XmlFile extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{
    const CONTENT_TYPE = 'text/xml';

    public function __construct($fileName, $fileData, $formatOutput = false)
    {
        $domDocument = new \DOMDocument();
        $domDocument->preserveWhiteSpace = false;
        if ($formatOutput) {
            $domDocument->formatOutput = true;
        }
        $domDocument->loadXML((string) $fileData);
        $fileData = $domDocument->saveXML();
        $domDocument = null;

        parent::__construct($fileName, $fileData, self::CONTENT_TYPE);
    }
}
