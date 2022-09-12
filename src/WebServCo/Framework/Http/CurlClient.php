<?php

declare(strict_types=1);

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

    public function retrieve(string $url): Response
    {
        if (!$url) {
            throw new \InvalidArgumentException('URL is empty');
        }

        $this->debugInit();

        $this->curl = \curl_init();
        /**
         * PHP 8 compatibility.
         *
         * https://www.php.net/manual/en/migration80.incompatible.php
         * "curl_init() will now return a CurlHandle object rather than a resource."
         * "Return value checks using is_resource() should be replaced with checks for false."
         * old code:
         * if (!\is_resource($this->curl)) {
         * Not using CurlHandle for the moment so that the code is still compatible with PHP 7.4
         */
        if (false === $this->curl) {
            // Not in the try/catch/finally block as there is nothing to close or debug at this point.
            throw new HttpClientException('Not a valid cURL resource.');
        }

        try {
            $this->setCurlOptions($url);

            $this->debugSetOptions();

            $this->handleRequestMethod();

            $this->setRequestHeaders();

            $this->processResponse();

            $body = \trim($this->response);

            $this->processResponseHeaders();

            $headers = \end($this->responseHeaders);
            return new Response(
                $body, // content
                $this->getHttpCode(), // statusCode
                \is_array($headers) ? $headers : [], // headers
            );
        } catch (HttpClientException $e) {
            // Re-throw same exception
            // try/catch/finally block used in order to close and debug curl handle in all situations
            throw new HttpClientException($e->getMessage(), $e);
        } finally {
            // Debug and close connection in any situation.

            $this->debugInfo = \curl_getinfo($this->curl);

            /**
             * PHP 8 compatibility.
             *
             * https://www.php.net/manual/en/migration80.incompatible.php
             * "The curl_close() function no longer has an effect,
             * instead the CurlHandle instance is automatically destroyed if it is no longer referenced. "
             * Use `unset` for PHP 8 compatibility (https://php.watch/versions/8.0/resource-CurlHandle)
             */
            \curl_close($this->curl);
            unset($this->curl);

            $this->debugFinish();
        }
    }

    protected function debugFinish(): bool
    {
        if ($this->debug) {
            \fclose($this->debugStderr);
            $this->debugOutput = (string) \ob_get_clean();

            $this->logger->debug('CURL INFO:', $this->debugInfo);
            $this->logger->debug('CURL VERBOSE:', ['debugOutput' => $this->debugOutput]);
            if ($this->requestData) {
                $this->logger->debug('REQUEST DATA:', ['requestData' => $this->requestData]);
            }
            if ($this->response) {
                $this->logger->debug('CURL RESPONSE:', ['response' => $this->response]);
            }

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

    protected function debugSetOptions(): bool
    {
        if ($this->debug) {
            //curl_setopt($this->curl, CURLINFO_HEADER_OUT, 1); /* verbose not working if this is enabled */
            \curl_setopt($this->curl, \CURLOPT_VERBOSE, 1);
            \curl_setopt($this->curl, \CURLOPT_STDERR, $this->debugStderr);
            return true;
        }
        return false;
    }

    protected function getHttpCode(): int
    {
        $httpCode = \curl_getinfo($this->curl, \CURLINFO_RESPONSE_CODE);
        if (!$httpCode) {
            throw new HttpClientException(\sprintf("Empty HTTP status code. cURL error: %s.", $this->curlError));
        }
        return $httpCode;
    }

    protected function handleRequestMethod(): bool
    {
        if (!\is_resource($this->curl)) {
            throw new HttpClientException('Not a valid resource.');
        }

        switch ($this->method) {
            case Method::DELETE:
            case Method::POST:
                /*
                * This sets the header application/x-www-form-urlencoded
                * Use custom request instead and handle headers manually
                * curl_setopt($this->curl, CURLOPT_POST, true);
                */
                \curl_setopt($this->curl, \CURLOPT_CUSTOMREQUEST, $this->method);
                if ($this->requestData) {
                    \curl_setopt($this->curl, \CURLOPT_POSTFIELDS, $this->requestData);
                    if (\is_array($this->requestData)) {
                        $this->setRequestHeader('Content-Type', 'multipart/form-data');
                    } else {
                        if ($this->requestContentType) {
                            $this->setRequestHeader('Content-Type', $this->requestContentType);
                        }
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
        if (!$headerDataTrimmed) {
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
            $this->responseHeaders[] = \WebServCo\Framework\Helpers\Http\HeadersHelper::parseArray($item);
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
                \CURLOPT_TIMEOUT => $this->timeout, // The maximum number of seconds to allow cURL functions to execute.
            ],
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
        if ($this->requestHeaders) {
            \curl_setopt(
                $this->curl,
                \CURLOPT_HTTPHEADER,
                $this->parseRequestHeaders($this->requestHeaders),
            );
        }

        // Callback to process response headers
        \curl_setopt($this->curl, \CURLOPT_HEADERFUNCTION, [$this, 'headerCallback']);

        return true;
    }
}
