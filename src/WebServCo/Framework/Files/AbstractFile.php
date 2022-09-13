<?php

namespace WebServCo\Framework\Files;

use WebServCo\Framework\Http\Response;

abstract class AbstractFile
{
    protected $fileName;
    protected $fileData;
    protected $contentType;

    public const CONTENT_TYPE = 'application/octet-stream';

    public function __construct($fileName, $fileData, $contentType = self::CONTENT_TYPE)
    {
        $this->fileName = (string) $fileName;
        $this->fileData = $fileData;
        $this->contentType = $contentType;
    }

    public function getContentType()
    {
        return $this->contentType;
    }

    public function getDownloadResponse()
    {
        return new Response(
            $this->fileData,
            200,
            [
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
                'ETag' => md5($this->fileData),
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public',
                'Content-Description' => 'File Transfer',
                'Content-Disposition' => sprintf(
                    'attachment; filename="%s"',
                    $this->fileName
                ),
                'Content-Type' => $this->contentType,
                'Content-Transfer-Encoding' => 'binary',
                'Connection' => 'close',
            ]
        );
    }

    public function getFileData()
    {
        return $this->fileData;
    }

    public function getFileName()
    {
        return $this->fileName;
    }

    public function getOutputResponse()
    {
        return new Response(
            $this->fileData,
            200,
            [
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
                'ETag' => md5($this->fileData),
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public',
                'Content-Type' => $this->contentType,
                'Content-Transfer-Encoding' => 'binary',
            ]
        );
    }
}
