<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

use WebServCo\Framework\Http\Response;

abstract class AbstractFile
{
    public const string CONTENT_TYPE = 'application/octet-stream';

    protected string $fileName;
    protected string $fileData;
    protected string $contentType;

    public function __construct(string $fileName, string $fileData, string $contentType = self::CONTENT_TYPE)
    {
        $this->fileName = $fileName;
        $this->fileData = $fileData;
        $this->contentType = $contentType;
    }

    public function getContentType(): string
    {
        return $this->contentType;
    }

    public function getDownloadResponse(): Response
    {
        return new Response(
            $this->fileData,
            200,
            [
                'Accept-Ranges' => ['bytes'],
                'Cache-Control' => ['public'],
                'Connection' => ['close'],
                'Content-Description' => ['File Transfer'],
                'Content-Disposition' => [\sprintf('attachment; filename="%s"', $this->fileName)],
                'Content-Transfer-Encoding' => ['binary'],
                'Content-Type' => [$this->contentType],
                'ETag' => [\md5($this->fileData)],
                'Last-Modified' => [\gmdate('D, d M Y H:i:s') . ' GMT'],
            ],
        );
    }

    public function getFileData(): string
    {
        return $this->fileData;
    }

    public function getFileName(): string
    {
        return $this->fileName;
    }

    public function getOutputResponse(): Response
    {
        return new Response(
            $this->fileData,
            200,
            [
                'Accept-Ranges' => ['bytes'],
                'Cache-Control' => ['public'],
                'Content-Transfer-Encoding' => ['binary'],
                'Content-Type' => [$this->contentType],
                'ETag' => [\md5($this->fileData)],
                'Last-Modified' => [\gmdate('D, d M Y H:i:s') . ' GMT'],
            ],
        );
    }

    public function setFileName(string $fileName): bool
    {
        $this->fileName = $fileName;
        return true;
    }
}
