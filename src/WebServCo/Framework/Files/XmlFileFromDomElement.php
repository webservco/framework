<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

final class XmlFileFromDomElement extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{

    public function __construct(string $fileName, \DOMElement $domElement, bool $formatOutput = false)
    {
        $domDocument = new \DOMDocument();
        $domDocument->preserveWhiteSpace = false;
        if ($formatOutput) {
            $domDocument->formatOutput = true;
        }
        $element = $domDocument->importNode($domElement, true);
        $domDocument->appendChild($element);
        $fileData = (string) $domDocument->saveXML();

        // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
        $domDocument = null;
        // phpcs:ignore SlevomatCodingStandard.Variables.UnusedVariable.UnusedVariable
        $element = null;

        parent::__construct($fileName, $fileData, XmlFile::CONTENT_TYPE);
    }
}
