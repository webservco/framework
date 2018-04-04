<?php
namespace WebServCo\Framework;

final class File extends AbstractFile
{
    public function __construct($fileName, $fileData, $contentType = 'application/octet-stream')
    {
        if (is_resource($fileData)) {
            $fileData = stream_get_contents($fileData);
        }
        parent::__construct($fileName, $fileData, $contentType);
    }
}
