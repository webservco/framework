<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

use WebServCo\Framework\Http\Response;

interface FileInterface
{
    public function getContentType(): string;
    public function getDownloadResponse(): Response;
    public function getFileData(): string;
    public function getFileName(): string;
    public function getOutputResponse(): Response;
}
