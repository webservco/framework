<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

class XmlFile extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{
    const CONTENT_TYPE = 'text/xml';

    public function __construct(string $fileName, string $fileData, bool $formatOutput = false)
    {
        $domDocument = new \DOMDocument;
        $domDocument->preserveWhiteSpace = false;
        if ($formatOutput) {
            $domDocument->formatOutput = true;
        }
        $domDocument->loadXML((string) $fileData);
        $fileData = (string) $domDocument->saveXML();
        $domDocument = null;

        parent::__construct($fileName, $fileData, self::CONTENT_TYPE);
    }
}
