<?php
namespace WebServCo\Framework\Files;

final class XmlFileFromDomElement extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{
    public function __construct($fileName, \DOMElement $domElement, $formatOutput = false)
    {
        $domDocument = new \DOMDocument;
        $domDocument->preserveWhiteSpace = false;
        if ($formatOutput) {
            $domDocument->formatOutput = true;
        }
        $domDocument->appendChild($domElement);
        $fileData = $domDocument->saveXML();
        $domDocument = null;

        parent::__construct($fileName, $fileData, XmlFile::CONTENT_TYPE);
    }
}
