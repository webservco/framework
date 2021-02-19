<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

use WebServCo\Framework\Http\Response;

abstract class AbstractFile
{

    public const CONTENT_TYPE = 'application/octet-stream';

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
                'Last-Modified' => [\gmdate('D, d M Y H:i:s') . ' GMT'],
                'ETag' => [\md5($this->fileData)],
                'Accept-Ranges' => ['bytes'],
                'Cache-Control' => ['public'],
                'Content-Description' => ['File Transfer'],
                'Content-Disposition' => [\sprintf('attachment; filename="%s"', $this->fileName)],
                'Content-Type' => [$this->contentType],
                'Content-Transfer-Encoding' => ['binary'],
                'Connection' => ['close'],
            ]
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
                'Last-Modified' => [\gmdate('D, d M Y H:i:s') . ' GMT'],
                'ETag' => [\md5($this->fileData)],
                'Accept-Ranges' => ['bytes'],
                'Cache-Control' => ['public'],
                'Content-Type' => [$this->contentType],
                'Content-Transfer-Encoding' => ['binary'],
            ]
        );
    }
}
