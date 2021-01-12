<?php
namespace WebServCo\Framework\Http;

class XSendFileResponse extends Response
{
    public function __construct(string $filePath, string $outputFilename)
    {
        if (!is_readable($filePath)) {
            throw new \WebServCo\Framework\Exceptions\NotFoundException('File not found.');
        }

        parent::__construct(
            '', // content
            200, // statusCode
            [
                'Content-Type' => ['application/octet-stream'],
                'Content-Disposition' => [sprintf('attachment; filename="%s"', $outputFilename)],
                'X-Sendfile' => [$filePath],
            ] // headers
        );
    }
}
