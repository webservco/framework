<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Http\Method;
use WebServCo\Framework\Exceptions\ApplicationException;

final class CurlBrowser implements
    \WebServCo\Framework\Interfaces\HttpBrowserInterface
{
    protected $curl;
    protected $curlError;
    protected $debug;
    protected $debugInfo;
    protected $debugOutput;
    protected $debugStderr;
    protected $logger;
    protected $method;
    protected $requestData;
    protected $requestHeaders;
    protected $requestContentType;
    protected $skipSslVerification;
    protected $response;
    protected $responseHeaders;
    protected $responseHeaderArray;
    protected $responseHeadersArray;

    public function __construct(\WebServCo\Framework\Interfaces\LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->debug = false;
        $this->skipSslVerification = false;
        $this->requestHeaders = [];
        // Default Content-Type for
        $this->requestContentType = 'application/x-www-form-urlencoded';
    }

    public function get($url)
    {
        $this->setMethod(Method::GET);
        return $this->retrieve($url);
    }

    public function getRequestHeaders()
    {
        return $this->requestHeaders;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function head($url)
    {
        $this->setMethod(Method::HEAD);
        return $this->retrieve($url);
    }

    public function post($url, $data = null)
    {
        $this->setMethod(Method::POST);
        if (!empty($data)) {
            $this->setRequestData($data);
        }
        return $this->retrieve($url);
    }

    public function retrieve($url)
    {
        $this->debugInit();

        $this->curl = curl_init();
        if (!is_resource($this->curl)) {
            throw new ApplicationException('Not a valid resource');
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
            throw new ApplicationException(sprintf("Empty HTTP status code. cURL error: %s", $this->curlError));
        }

        $body = trim($this->response);

        $this->processResponseHeaders();

        $this->debugFinish();

        return new \WebServCo\Framework\Http\Response(
            $body,
            $httpCode,
            end($this->responseHeaders)
        );
    }

    public function setDebug($debug)
    {
        $this->debug = (bool) $debug;
    }

    public function setMethod($method)
    {
        if (!in_array($method, Method::getSupported())) {
            throw new ApplicationException('Unsupported method');
        }
        $this->method = $method;
        return true;
    }

    public function setRequestData($data)
    {
        if (is_array($data)) {
            $this->requestData = [];
            foreach ($data as $key => $value) {
                if (is_array($value)) {
                    throw new \InvalidArgumentException('POST value can not be an array');
                }
                $this->requestData[$key] = $value;
            }
            return true;
        }
        $this->requestData = $data;
        return true;
    }

    public function setRequestContentType($contentType)
    {
        $this->requestContentType = $contentType;
    }

    public function setRequestHeader($name, $value)
    {
        $this->requestHeaders[$name] = $value;
    }

    public function setSkipSSlVerification($skipSslVerification)
    {
        $this->skipSslVerification = (bool) $skipSslVerification;
    }

    protected function debugDo()
    {
        if ($this->debug) {
            //curl_setopt($this->curl, CURLINFO_HEADER_OUT, 1); /* verbose not working if this is enabled */
            curl_setopt($this->curl, CURLOPT_VERBOSE, 1);
            curl_setopt($this->curl, CURLOPT_STDERR, $this->debugStderr);
            return false;
        }
        return false;
    }

    protected function debugFinish()
    {
        if ($this->debug) {
            fclose($this->debugStderr);
            $this->debugOutput = ob_get_clean();

            $this->logger->debug('CURL INFO:', $this->debugInfo);
            $this->logger->debug('CURL VERBOSE:', $this->debugOutput);
            $this->logger->debug('CURL RESPONSE:', $this->response);

            return true;
        }
        return false;
    }

    protected function debugInit()
    {
        if ($this->debug) {
            ob_start();
            $this->debugStderr = fopen('php://output', 'w');
            return true;
        }
        return false;
    }

    protected function getHttpCode()
    {
        return isset($this->debugInfo['http_code']) ? $this->debugInfo['http_code']: false;
    }

    protected function handleRequestMethod()
    {
        if (!is_resource($this->curl)) {
            throw new ApplicationException('Not a valid resource');
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
                        $this->setRequestHeader('Content-Length', strlen($this->requestData));
                    }
                }
                break;
            case Method::HEAD:
                curl_setopt($this->curl, CURLOPT_NOBODY, true);
                break;
        }
    }

    protected function headerCallback($curlResource, $headerData)
    {
        $headerDataTrimmed = trim($headerData);
        if (empty($headerDataTrimmed)) {
            $this->responseHeadersArray[] = $this->responseHeaderArray;
            $this->responseHeaderArray = [];
        }
        $this->responseHeaderArray[] = $headerData;

        return strlen($headerData);
    }

    protected function parseRequestHeaders($headers)
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

    protected function parseResponseHeadersArray($responseHeadersArray = [])
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
                            //check if we already have a cookie with the same name
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
                $headers[$key] = trim($value);
            }
        }
        return $headers;
    }

    protected function processResponse()
    {
        $this->response = curl_exec($this->curl);
        $this->curlError = curl_error($this->curl);
        if (false === $this->response) {
            throw new ApplicationException(sprintf("cURL error: %s", $this->curlError));
        }
    }

    protected function processResponseHeaders()
    {
        $this->responseHeaders = [];
        foreach ($this->responseHeadersArray as $item) {
            $this->responseHeaders[] = $this->parseResponseHeadersArray($item);
        }
    }

    protected function setCurlOptions($url)
    {
        if (!is_resource($this->curl)) {
            throw new ApplicationException('Not a valid resource');
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

    protected function setRequestHeaders()
    {
        if (!is_resource($this->curl)) {
            throw new ApplicationException('Not a valid resource');
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
