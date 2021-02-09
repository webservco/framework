<?php declare(strict_types = 1);

namespace WebServCo\Framework\Http;

use WebServCo\Framework\Exceptions\HttpClientException;

final class CurlClient extends AbstractClient implements \WebServCo\Framework\Interfaces\HttpClientInterface
{

    /**
    * cURL
    *
    * @var resource
    */
    protected $curl;

    protected string $curlError;

    /**
    * Debug info
    *
    * @var array<string,string>
    */
    protected array $debugInfo;

    protected string $debugOutput;

    /**
    * debugStderr
    *
    * @var resource
    */
    protected $debugStderr;

    protected \WebServCo\Framework\Interfaces\LoggerInterface $logger;

    protected string $response;

    /**
    * Response header array
    *
    * @var array<int,string>
    */
    protected array $responseHeaderArray;

    /**
    * Response headers array
    *
    * @var array<int,array<int,string>>
    */
    protected array $responseHeadersArray;

    public function __construct(\WebServCo\Framework\Interfaces\LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->debug = false;
        $this->skipSslVerification = false;
        $this->requestHeaders = [];
        // Default Content-Type for
        $this->requestContentType = 'application/x-www-form-urlencoded';
    }

    public function retrieve(string $url): Response
    {
        $this->debugInit();

        $this->curl = \curl_init();
        if (!\is_resource($this->curl)) {
            throw new HttpClientException('Not a valid cURL resource.');
        }

        $this->setCurlOptions($url);

        $this->debugDo();

        $this->handleRequestMethod();

        $this->setRequestHeaders();

        $this->processResponse();

        $this->debugInfo = \curl_getinfo($this->curl);

        \curl_close($this->curl);

        $httpCode = $this->getHttpCode();

        if (empty($httpCode)) {
            throw new HttpClientException(\sprintf("Empty HTTP status code. cURL error: %s.", $this->curlError));
        }

        $body = \trim($this->response);

        $this->processResponseHeaders();

        $this->debugFinish();

        $headers = \end($this->responseHeaders);
        return new Response(
            $body,
            $httpCode,
            \is_array($headers) ? $headers : []
        );
    }

    protected function debugDo(): bool
    {
        if ($this->debug) {
            //curl_setopt($this->curl, CURLINFO_HEADER_OUT, 1); /* verbose not working if this is enabled */
            \curl_setopt($this->curl, \CURLOPT_VERBOSE, 1);
            \curl_setopt($this->curl, \CURLOPT_STDERR, $this->debugStderr);
            return true;
        }
        return false;
    }

    protected function debugFinish(): bool
    {
        if ($this->debug) {
            \fclose($this->debugStderr);
            $this->debugOutput = (string) \ob_get_clean();

            $this->logger->debug('CURL INFO:', $this->debugInfo);
            $this->logger->debug('CURL VERBOSE:', $this->debugOutput);
            if (!empty($this->requestData)) {
                $this->logger->debug('REQUEST DATA:', $this->requestData);
            }
            $this->logger->debug('CURL RESPONSE:', $this->response);

            return true;
        }
        return false;
    }

    protected function debugInit(): bool
    {
        if ($this->debug) {
            \ob_start();
            $debugStderr = \fopen('php://output', 'w');
            if ($debugStderr) {
                $this->debugStderr = $debugStderr;
                return true;
            }
        }
        return false;
    }

    protected function getHttpCode(): int
    {
        return isset($this->debugInfo['http_code'])
            ? (int) $this->debugInfo['http_code']
            : 0;
    }

    protected function handleRequestMethod(): bool
    {
        if (!\is_resource($this->curl)) {
            throw new HttpClientException('Not a valid resource.');
        }

        switch ($this->method) {
            case Method::POST:
                /*
                * This sets the header application/x-www-form-urlencoded
                * Use custom request instead and handle headers manually
                * curl_setopt($this->curl, CURLOPT_POST, true);
                */
                \curl_setopt($this->curl, \CURLOPT_CUSTOMREQUEST, $this->method);
                if (!empty($this->requestData)) {
                    \curl_setopt($this->curl, \CURLOPT_POSTFIELDS, $this->requestData);
                    if (\is_array($this->requestData)) {
                        $this->setRequestHeader('Content-Type', 'multipart/form-data');
                    } else {
                        $this->setRequestHeader('Content-Type', $this->requestContentType);
                        // use strlen and not mb_strlen: "The length of the request body in octets (8-bit bytes)."
                        $this->setRequestHeader('Content-Length', (string) \strlen($this->requestData));
                    }
                }
                break;
            case Method::HEAD:
                \curl_setopt($this->curl, \CURLOPT_NOBODY, true);
                break;
        }

        return true;
    }

    /**
    * @param resource $curlResource
    */
    // phpcs:ignore SlevomatCodingStandard.Functions.UnusedParameter.UnusedParameter
    protected function headerCallback($curlResource, string $headerData): int
    {
        $headerDataTrimmed = \trim($headerData);
        if (empty($headerDataTrimmed)) {
            $this->responseHeadersArray[] = $this->responseHeaderArray;
            $this->responseHeaderArray = [];
        }
        $this->responseHeaderArray[] = $headerData;

        return \strlen($headerData);
    }

    /**
    * @param array<string,mixed> $headers
    * @return array<int,string>
    */
    protected function parseRequestHeaders(array $headers): array
    {
        $data = [];
        foreach ($headers as $k => $v) {
            if (\is_array($v)) {
                foreach ($v as $item) {
                    $data[] = \sprintf('%s: %s', $k, $item);
                }
            } else {
                $data[] = \sprintf('%s: %s', $k, $v);
            }
        }
        return $data;
    }

    /**
    * @param array<int,string> $responseHeadersArray
    * @return array<string,mixed>
    */
    protected function parseResponseHeadersArray(array $responseHeadersArray = []): array
    {
        $headers = [];

        foreach ($responseHeadersArray as $index => $line) {
            if (0 === $index) {
                continue; /* we'll get the status code elsewhere */
            }
            $parts = \explode(': ', $line, 2);
            if (!isset($parts[1])) {
                continue; // invalid header (missing colon)
            }
            [$key, $value] = $parts;
            if (isset($headers[$key])) {
                if (!\is_array($headers[$key])) {
                    $headers[$key] = [$headers[$key]];
                }
                // check cookies
                if ('Set-Cookie' === $key) {
                    $parts = \explode('=', $value, 2);
                    $cookieName = $parts[0];
                    if (\is_array($headers[$key])) {
                        foreach ($headers[$key] as $cookieIndex => $existingCookie) {
                            // check if we already have a cookie with the same name
                            if (0 !== \mb_stripos($existingCookie, $cookieName)) {
                                continue;
                            }

                            // remove previous cookie with the same name
                            unset($headers[$key][$cookieIndex]);
                        }
                    }
                }
                $headers[$key][] = \trim($value);
                $headers[$key] = \array_values((array) $headers[$key]); // re-index array
            } else {
                $headers[$key][] = \trim($value);
            }
        }
        return $headers;
    }

    protected function processResponse(): bool
    {
        $response = \curl_exec($this->curl);
        $this->curlError = \curl_error($this->curl);
        if (false === $response) {
            throw new HttpClientException(\sprintf("cURL error: %s.", $this->curlError));
        }
        $this->response = (string) $response;
        return true;
    }

    protected function processResponseHeaders(): bool
    {
        $this->responseHeaders = [];
        foreach ($this->responseHeadersArray as $item) {
            $this->responseHeaders[] = $this->parseResponseHeadersArray($item);
        }
        return true;
    }

    protected function setCurlOptions(string $url): bool
    {
        if (!\is_resource($this->curl)) {
            throw new HttpClientException('Not a valid resource.');
        }

        // set options
        \curl_setopt_array(
            $this->curl,
            [
                \CURLOPT_RETURNTRANSFER => true, /* return instead of outputting */
                \CURLOPT_URL => $url,
                \CURLOPT_HEADER => false, /* do not include the header in the output */
                \CURLOPT_FOLLOWLOCATION => true, /* follow redirects */
                \CURLOPT_CONNECTTIMEOUT => 60, // The number of seconds to wait while trying to connect.
                \CURLOPT_TIMEOUT => 60, // The maximum number of seconds to allow cURL functions to execute.
            ]
        );
        // check if we should ignore ssl errors
        if ($this->skipSslVerification) {
            // stop cURL from verifying the peer's certificate
            \curl_setopt($this->curl, \CURLOPT_SSL_VERIFYPEER, false);
            // don't check the existence of a common name in the SSL peer certificate
            \curl_setopt($this->curl, \CURLOPT_SSL_VERIFYHOST, 0);
        }
        return true;
    }

    protected function setRequestHeaders(): bool
    {
        if (!\is_resource($this->curl)) {
            throw new HttpClientException('Not a valid resource.');
        }

        // set headers
        if (!empty($this->requestHeaders)) {
            \curl_setopt(
                $this->curl,
                \CURLOPT_HTTPHEADER,
                $this->parseRequestHeaders($this->requestHeaders)
            );
        }

        // Callback to process response headers
        \curl_setopt($this->curl, \CURLOPT_HEADERFUNCTION, [$this, 'headerCallback']);

        return true;
    }
}
