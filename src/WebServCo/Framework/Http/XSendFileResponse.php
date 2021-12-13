<?php

declare(strict_types=1);

namespace WebServCo\Framework\Http;

class XSendFileResponse extends Response
{
    public function __construct(
        string $filePath,
        string $outputFilename,
        string $contentType = 'application/octet-stream'
    ) {
        if (!\is_readable($filePath)) {
            throw new \WebServCo\Framework\Exceptions\NotFoundException('File not found.');
        }

        parent::__construct(
            '', // content
            200, // statusCode
            [
                'Content-Type' => [$contentType],
                'Content-Disposition' => [\sprintf('attachment; filename="%s"', $outputFilename)],
                'X-Sendfile' => [$filePath],
            ], // headers
        );
    }
}
