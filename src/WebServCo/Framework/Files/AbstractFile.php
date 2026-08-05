<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

use WebServCo\Framework\Http\Response;

use function finfo_buffer;
use function finfo_open;
use function gmdate;
use function md5;
use function sprintf;

use const FILEINFO_MIME_TYPE;

abstract class AbstractFile
{
    public const string CONTENT_TYPE = 'application/octet-stream';

    public function __construct(
        protected string $fileName,
        protected string $fileData,
        protected ?string $contentType = self::CONTENT_TYPE,
    ) {
        if ($contentType !== null) {
            return;
        }

        $this->contentType = finfo_buffer(finfo_open(FILEINFO_MIME_TYPE), $fileData);
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
                'Content-Disposition' => [sprintf('attachment; filename="%s"', $this->fileName)],
                'Content-Transfer-Encoding' => ['binary'],
                'Content-Type' => [$this->contentType],
                'ETag' => [md5($this->fileData)],
                'Last-Modified' => [gmdate('D, d M Y H:i:s') . ' GMT'],
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
                'ETag' => [md5($this->fileData)],
                'Last-Modified' => [gmdate('D, d M Y H:i:s') . ' GMT'],
            ],
        );
    }

    public function setFileName(string $fileName): bool
    {
        $this->fileName = $fileName;

        return true;
    }
}
