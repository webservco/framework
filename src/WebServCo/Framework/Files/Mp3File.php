<?php

namespace WebServCo\Framework\Files;

class Mp3File extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{
    const CONTENT_TYPE = 'audio/mpeg';

    public function __construct($fileName, $fileData)
    {
        parent::__construct($fileName, $fileData, self::CONTENT_TYPE);
    }
}
