<?php

declare(strict_types=1);

namespace WebServCo\Framework\Http;

use WebServCo\Framework\AbstractResponse;
use WebServCo\Framework\Exceptions\HttpStatusCodeException;
use WebServCo\Framework\Interfaces\ResponseInterface;

use function array_key_exists;
use function array_keys;
use function header;
use function implode;
use function is_string;
use function sprintf;
use function strlen;
use function strtolower;

// @phpcs:ignore SlevomatCodingStandard.Classes.RequireAbstractOrFinal.ClassNeitherAbstractNorFinal
class Response extends AbstractResponse implements
    ResponseInterface
{
    protected string $statusText;

    /**
     * Headers
     *
     * @var array<string,array<string,bool>>
     */
    protected array $headers;
    protected bool $appendCharset;
    protected string $charset;

    /**
    * @param array<string,array<int,string>> $headers
    */
    public function __construct(string $content, int $statusCode = 200, array $headers = [])
    {
        $this->appendCharset = false;
        $this->setStatus($statusCode);

        $this->charset = 'utf-8';

        $this->headers = [];

        foreach ($headers as $name => $data) {
            /*
            Make sure header names are lower case, to prevent inconsistency problems.
            https://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2
            */
            $headerName = strtolower($name);
            foreach ($data as $value) {
                $this->setHeader($headerName, $value);
            }
        }

        $this->setContent($content);
    }

    /**
    * @return array<int,string>
    */
    public function getHeader(string $name): array
    {
        $name = strtolower($name);
        if (!array_key_exists($name, $this->headers)) {
            return [];
        }

        // because use value as key ...
        return array_keys($this->headers[$name]);
    }

    public function getHeaderLine(string $name): string
    {
        $data = $this->getHeader($name);
        if (!$data) {
            return '';
        }

        return implode(', ', $data);
    }

    public function setStatus(int $statusCode): bool
    {
        $statusCodes = StatusCode::getSupported();
        if (!array_key_exists($statusCode, $statusCodes)) {
            throw new HttpStatusCodeException(
                sprintf('Invalid HTTP status code: %s.', $statusCode),
            );
        }
        $this->statusCode = $statusCode;
        $this->statusText = $statusCodes[$statusCode];

        return true;
    }

    public function setHeader(string $name, string $value): bool
    {
        if ($this->appendCharset) {
            switch ($name) {
                case 'Content-Type':
                    if (is_string($value)) {
                        $value .= '; charset=' . $this->charset;
                    }

                    break;
            }
        }
        // use value as key of all values for easier management
        $this->headers[$name][$value] = true;

        return true;
    }

    public function send(): int
    {
        $this->sendHeaders();
        $this->sendContent();

        return $this->statusCode;
    }

    protected function sendHeader(string $name, string $value, int $statusCode): bool
    {
        header(
            sprintf('%s: %s', $name, $value),
            true,
            $statusCode,
        );

        return true;
    }

    protected function sendHeaders(): bool
    {
        foreach ($this->headers as $name => $data) {
            foreach ($data as $value => $bool) {
                $this->sendHeader($name, $value, $this->statusCode);
            }
        }
        // use strlen and not mb_strlen: "The length of the request body in octets (8-bit bytes)."
        $this->sendHeader('Content-length', (string) strlen($this->content), $this->statusCode);

        header(
            sprintf('HTTP/1.1 %s %s', $this->statusCode, $this->statusText),
            true,
            $this->statusCode,
        );

        return true;
    }

    protected function sendContent(): bool
    {
        echo $this->content;

        return true;
    }
}
