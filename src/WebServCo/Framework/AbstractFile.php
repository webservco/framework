<?php
namespace WebServCo\Framework;

abstract class AbstractFile
{
    protected $fileName;
    protected $fileData;
    protected $contentType;

    public function __construct(string $fileName, string $fileData, string $contentType = 'application/octet-stream')
    {
        $this->fileName = $fileName;
        $this->fileData = $fileData;
        $this->contentType = $contentType;
    }

    public function getDownloadResponse()
    {
        return new \WebServCo\Framework\HttpResponse(
            $this->fileData,
            200,
            [
                'Last-Modified' => gmdate('D, d M Y H:i:s') . ' GMT',
                'ETag' => md5($this->fileData),
                'Accept-Ranges' => 'bytes',
                'Cache-Control' => 'public',
                'Content-Description' => 'File Transfer',
                'Content-Disposition' => sprintf(
                    'attachment; filename=%s',
                    $this->fileName
                ),
                'Content-Type' => $this->contentType,
                'Content-Transfer-Encoding' => 'binary',
                'Connection' => 'close',
            ]
        );
    }
}
