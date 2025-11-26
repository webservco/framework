<?php

declare(strict_types=1);

namespace WebServCo\Framework\Http;

use WebServCo\Framework\Exceptions\NotFoundException;

use function is_readable;
use function sprintf;

final class XSendFileResponse extends Response
{
    public function __construct(
        string $filePath,
        string $outputFilename,
        string $contentType = 'application/octet-stream',
    ) {
        if (!is_readable($filePath)) {
            throw new NotFoundException('File not found.');
        }

        parent::__construct(
            // content
            '',
            // statusCode
            200,
            [
                'Content-Disposition' => [sprintf('attachment; filename="%s"', $outputFilename)],
                'Content-Type' => [$contentType],
                'X-Sendfile' => [$filePath],
            // headers
            ],
        );
    }
}
