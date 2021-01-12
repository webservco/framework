<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Http\Response;
use WebServCo\Framework\Http\Method;
use WebServCo\Framework\Exceptions\HttpBrowserException;

final class CurlBrowser implements
    \WebServCo\Framework\Interfaces\HttpBrowserInterface
{
    /**
    * @var resource
    */
    protected $curl;
    protected string $curlError;
    protected bool $debug;
    /**
    * @var array<string,string>
    */
    protected array $debugInfo;
    protected string $debugOutput;
    /**
    * @var resource
    */
    protected $debugStderr;
    protected \WebServCo\Framework\Interfaces\LoggerInterface $logger;
    protected string $method;
    /**
    * @var array<string,string>|string
    */
    protected $requestData;
    /**
    * @var array<string,mixed>
    */
    protected array $requestHeaders;
    protected string $requestContentType;
    protected bool $skipSslVerification;
    protected string $response;
    /**
    * @var array<int,array<string,mixed>>
    */
    protected array $responseHeaders;
    /**
    * @var array<int,string>
    */
    protected array $responseHeaderArray;
    /**
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

    public function get(string $url) : Response
    {
        $this->setMethod(Method::GET);
        return $this->retrieve($url);
    }

    /**
    * @return array<string,string>
    */
    public function getRequestHeaders() : array
    {
        return $this->requestHeaders;
    }

    /**
    * @return array<int,array<string,mixed>>
    */
    public function getResponseHeaders() : array
    {
        return $this->responseHeaders;
    }

    public function head(string $url) : Response
    {
        $this->setMethod(Method::HEAD);
        return $this->retrieve($url);
    }

    /**
    * @param string $url
    * @param array<mixed>|string $data
    * @return Response
    */
    public function post(string $url, $data = null) : Response
    {
        $this->setMethod(Method::POST);
        if (!empty($data)) {
            $this->setRequestData($data);
        }
        return $this->retrieve($url);
    }

    public function retrieve(string $url) : Response
    {
        $this->debugInit();

        $this->curl = curl_init();
        if (!is_resource($this->curl)) {
            throw new HttpBrowserException('Not a valid cURL resource.');
        }

        $this->setCurlOptions($url);

        $this->debugDo();

        $this->handleRequestMethod();

        $this->setRequestHeaders();

        $this->processResponse();

        $this->debugInfo = curl_getinfo($this->curl);

        curl_close($this->curl);

        $httpCode = $this->getHttpCode();

        if (empty($httpCode)) {
            throw new HttpBrowserException(sprintf("Empty HTTP status code. cURL error: %s.", $this->curlError));
        }

        $body = trim($this->response);

        $this->processResponseHeaders();

        $this->debugFinish();

        return new Response(
            $body,
            $httpCode,
            end($this->responseHeaders) ?: []
        );
    }

    public function setDebug(bool $debug) : bool
    {
        $this->debug = $debug;
        return true;
    }

    public function setMethod(string $method) : bool
    {
        if (!in_array($method, Method::getSupported())) {
            throw new HttpBrowserException('Unsupported method.');
        }
        $this->method = $method;
        return true;
    }

    /**
    * @param array<string,mixed>|string $data
    * @return bool
    */
    public function setRequestData($data) : bool
    {
        if (is_array($data)) {
            $this->requestData = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    throw new \InvalidArgumentException('Request data value can not be an array.');
                }
                $this->requestData[$key] = $value;
            }
            return true;
        }
        $this->requestData = $data;
        return true;
    }

    public function setRequestContentType(string $contentType) : bool
    {
        $this->requestContentType = $contentType;
        return true;
    }

    public function setRequestHeader(string $name, string $value) : bool
    {
        $this->requestHeaders[$name] = $value;
        return true;
    }

    public function setSkipSSlVerification(bool $skipSslVerification) : bool
    {
        $this->skipSslVerification = $skipSslVerification;
        return true;
    }

    protected function debugDo() : bool
    {
        if ($this->debug) {
            //curl_setopt($this->curl, CURLINFO_HEADER_OUT, 1); /* verbose not working if this is enabled */
            curl_setopt($this->curl, CURLOPT_VERBOSE, 1);
            curl_setopt($this->curl, CURLOPT_STDERR, $this->debugStderr);
            return true;
        }
        return false;
    }

    protected function debugFinish() : bool
    {
        if ($this->debug) {
            fclose($this->debugStderr);
            $this->debugOutput = (string) ob_get_clean();

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

    protected function debugInit() : bool
    {
        if ($this->debug) {
            ob_start();
            $debugStderr = fopen('php://output', 'w');
            if ($debugStderr) {
                $this->debugStderr = $debugStderr;
                return true;
            }
        }
        return false;
    }

    protected function getHttpCode() : int
    {
        return isset($this->debugInfo['http_code']) ? (int) $this->debugInfo['http_code']: 0;
    }

    protected function handleRequestMethod() : void
    {
        if (!is_resource($this->curl)) {
            throw new HttpBrowserException('Not a valid resource.');
        }

        switch ($this->method) {
            case Method::POST:
                /*
                * This sets the header application/x-www-form-urlencoded
                * Use custom request instead and handle headers manually
                * curl_setopt($this->curl, CURLOPT_POST, true);
                */
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, $this->method);
                if (!empty($this->requestData)) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->requestData);
                    if (is_array($this->requestData)) {
                        $this->setRequestHeader('Content-Type', 'multipart/form-data');
                    } else {
                        $this->setRequestHeader('Content-Type', $this->requestContentType);
                        // use strlen and not mb_strlen: "The length of the request body in octets (8-bit bytes)."
                        $this->setRequestHeader('Content-Length', (string) strlen($this->requestData));
                    }
                }
                break;
            case Method::HEAD:
                curl_setopt($this->curl, CURLOPT_NOBODY, true);
                break;
        }
    }

    /**
    * @param resource $curlResource
    * @param string $headerData
    * @return int
    */
    protected function headerCallback($curlResource, $headerData) : int
    {
        $headerDataTrimmed = trim($headerData);
        if (empty($headerDataTrimmed)) {
            $this->responseHeadersArray[] = $this->responseHeaderArray;
            $this->responseHeaderArray = [];
        }
        $this->responseHeaderArray[] = $headerData;

        return strlen($headerData);
    }

    /**
    * @param array<string,mixed> $headers
    * @return array<int,string>
    */
    protected function parseRequestHeaders(array $headers) : array
    {
        $data = [];
        foreach ($headers as $k => $v) {
            if (is_array($v)) {
                foreach ($v as $item) {
                    $data[] = sprintf('%s: %s', $k, $item);
                }
            } else {
                $data[] = sprintf('%s: %s', $k, $v);
            }
        }
        return $data;
    }

    /**
    * @param array<int,string> $responseHeadersArray
    * @return array<string,mixed>
    */
    protected function parseResponseHeadersArray(array $responseHeadersArray = []) : array
    {
        $headers = [];

        foreach ($responseHeadersArray as $index => $line) {
            if (0 === $index) {
                continue; /* we'll get the status code elsewhere */
            }
            $parts = explode(': ', $line, 2);
            if (!isset($parts[1])) {
                continue; // invalid header (missing colon)
            }
            list($key, $value) = $parts;
            if (isset($headers[$key])) {
                if (!is_array($headers[$key])) {
                    $headers[$key] = [$headers[$key]];
                }
                // check cookies
                if ('Set-Cookie' == $key) {
                    $parts = explode('=', $value, 2);
                    $cookieName = $parts[0];
                    if (is_array($headers[$key])) {
                        foreach ($headers[$key] as $cookieIndex => $existingCookie) {
                            // check if we already have a cookie with the same name
                            if (0 === mb_stripos($existingCookie, $cookieName)) {
                                // remove previous cookie with the same name
                                unset($headers[$key][$cookieIndex]);
                            }
                        }
                    }
                }
                $headers[$key][] = trim($value);
                $headers[$key] = array_values((array) $headers[$key]); // re-index array
            } else {
                $headers[$key][] = trim($value);
            }
        }
        return $headers;
    }

    protected function processResponse() : void
    {
        $response = curl_exec($this->curl);
        $this->curlError = curl_error($this->curl);
        if (false === $response) {
            throw new HttpBrowserException(sprintf("cURL error: %s.", $this->curlError));
        }
        $this->response = (string) $response;
    }

    protected function processResponseHeaders() : void
    {
        $this->responseHeaders = [];
        foreach ($this->responseHeadersArray as $item) {
            $this->responseHeaders[] = $this->parseResponseHeadersArray($item);
        }
    }

    protected function setCurlOptions(string $url) : void
    {
        if (!is_resource($this->curl)) {
            throw new HttpBrowserException('Not a valid resource.');
        }

        // set options
        curl_setopt_array(
            $this->curl,
            [
                CURLOPT_RETURNTRANSFER => true, /* return instead of outputting */
                CURLOPT_URL => $url,
                CURLOPT_HEADER => false, /* do not include the header in the output */
                CURLOPT_FOLLOWLOCATION => true, /* follow redirects */
                CURLOPT_CONNECTTIMEOUT => 60, // The number of seconds to wait while trying to connect.
                CURLOPT_TIMEOUT => 60, // The maximum number of seconds to allow cURL functions to execute.
            ]
        );
        // check if we should ignore ssl errors
        if ($this->skipSslVerification) {
            // stop cURL from verifying the peer's certificate
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
            // don't check the existence of a common name in the SSL peer certificate
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        }
    }

    protected function setRequestHeaders() : void
    {
        if (!is_resource($this->curl)) {
            throw new HttpBrowserException('Not a valid resource.');
        }

        // set headers
        if (!empty($this->requestHeaders)) {
            curl_setopt(
                $this->curl,
                CURLOPT_HTTPHEADER,
                $this->parseRequestHeaders($this->requestHeaders)
            );
        }

        // Callback to process response headers
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, [$this, 'headerCallback']);
    }
}
