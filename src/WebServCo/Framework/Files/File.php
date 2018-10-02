<?php
namespace WebServCo\Framework\Files;

final class File extends AbstractFile
{
    public function __construct($fileName, $fileData, $contentType = 'application/octet-stream')
    {
        if (is_null($fileName)) {
            throw new \WebServCo\Framework\Exceptions\FileException('File name is NULL');
        }
        if (is_resource($fileData)) {
            $fileData = stream_get_contents($fileData);
        }
        parent::__construct($fileName, $fileData, $contentType);
    }
}
