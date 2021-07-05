<?php

declare(strict_types=1);

namespace WebServCo\Framework\Http;

use WebServCo\Framework\Exceptions\HttpClientException;

abstract class AbstractClient
{
    protected bool $debug;

    protected \WebServCo\Framework\Interfaces\LoggerInterface $logger;

    protected string $method;

    protected string $requestContentType;

    /**
    * Request data
    *
    * @var array<string,string>|string
    */
    protected $requestData;

    /**
    * Request headers
    *
    * @var array<string,mixed>
    */
    protected array $requestHeaders;

    protected string $response;

    /**
    * Resposne headers
    *
    * @var array<int,array<string,mixed>>
    */
    protected array $responseHeaders;

    protected bool $skipSslVerification;

    protected int $timeout;

    abstract public function retrieve(string $url): Response;

    public function __construct(\WebServCo\Framework\Interfaces\LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->debug = false;
        $this->skipSslVerification = false;
        $this->requestHeaders = [];
        // Default Content-Type for
        $this->requestContentType = 'application/x-www-form-urlencoded';
        $this->response = '';
        $this->timeout = 60;
    }

    public function get(string $url): Response
    {
        $this->setMethod(Method::GET);
        return $this->retrieve($url);
    }

    /**
    * @return array<string,string>
    */
    public function getRequestHeaders(): array
    {
        return $this->requestHeaders;
    }

    /**
    * @return array<int,array<string,mixed>>
    */
    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    public function head(string $url): Response
    {
        $this->setMethod(Method::HEAD);
        return $this->retrieve($url);
    }

    /**
    * @param array<string,mixed>|string $data
    */
    public function post(string $url, $data = null): Response
    {
        $this->setMethod(Method::POST);
        if ($data) {
            $this->setRequestData($data);
        }
        return $this->retrieve($url);
    }

    public function setDebug(bool $debug): bool
    {
        $this->debug = $debug;
        return true;
    }

    public function setMethod(string $method): bool
    {
        if (!\in_array($method, Method::getSupported(), true)) {
            throw new HttpClientException('Unsupported method.');
        }
        $this->method = $method;
        return true;
    }

    /**
    * @param array<string,mixed>|string $data
    */
    public function setRequestData($data): bool
    {
        if (\is_array($data)) {
            $this->requestData = [];
            foreach ($data as $key => $value) {
                if (\is_array($value)) {
                    throw new \InvalidArgumentException('Request data value can not be an array.');
                }
                $this->requestData[$key] = $value;
            }
            return true;
        }
        $this->requestData = $data;
        return true;
    }

    public function setRequestContentType(string $contentType): bool
    {
        $this->requestContentType = $contentType;
        return true;
    }

    public function setRequestHeader(string $name, string $value): bool
    {
        $this->requestHeaders[$name] = $value;
        return true;
    }

    public function setSkipSSlVerification(bool $skipSslVerification): bool
    {
        $this->skipSslVerification = $skipSslVerification;
        return true;
    }

    public function setTimeout(int $timeout): bool
    {
        $this->timeout = $timeout;
        return true;
    }
}
