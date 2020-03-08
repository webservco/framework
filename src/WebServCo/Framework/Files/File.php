<?php
namespace WebServCo\Framework\Files;

class File extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{
    public function __construct($fileName, $fileData, $contentType = self::CONTENT_TYPE)
    {
        if (is_null($fileName)) {
            throw new \WebServCo\Framework\Exceptions\FileException('File name is NULL.');
        }
        if (is_resource($fileData)) {
            $fileData = stream_get_contents($fileData);
        }
        parent::__construct($fileName, $fileData, $contentType);
    }
}
