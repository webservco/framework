<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Http;
use WebServCo\Framework\Exceptions\ApplicationException;

final class CurlBrowser implements
    \WebServCo\Framework\Interfaces\HttpBrowserInterface
{
    protected $debug = true;
    protected $requestHeaders = [];

    protected $method;
    protected $postData;

    protected $curl;
    protected $debugStderr;
    protected $debugOutput;
    protected $debugInfo;
    protected $response;

    protected $logger;

    public function __construct($logDir, \WebServCo\Framework\Interfaces\RequestInterface $requestInterface)
    {
        $this->logger = new \WebServCo\Framework\FileLogger(
            'CurlBrowser',
            $logDir,
            $requestInterface
        );
        $this->logger->clear();
    }

    public function setDebug(bool $debug)
    {
        $this->debug = $debug;
    }

    public function setRequestHeaders(array $requestHeaders)
    {
        $this->requestHeaders = $requestHeaders;
    }

    public function get($url)
    {
        $this->setMethod(Http::METHOD_GET);
        return $this->retrieve($url);
    }

    public function post($url, $postData = [])
    {
        $this->setMethod(Http::METHOD_POST);
        $this->setPostData($postData);
        return $this->retrieve($url);
    }

    protected function setMethod($method)
    {
        if (!in_array($method, Http::getMethods())) {
            throw new ApplicationException('Unsupported method');
        }
        $this->method = $method;
        return true;
    }

    protected function setPostData(array $postData)
    {
        $this->postData = $postData;
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

    protected function getHttpCode()
    {
        return isset($this->debugInfo['http_code']) ? $this->debugInfo['http_code']: false;
    }

    protected function parseResponseHeaders($headerString)
    {
        $headers = [];
        $lines = explode("\r\n", $headerString);
        foreach ($lines as $index => $line) {
            if (0 === $index) {
                continue; /* we'll get the status code elsewhere */
            }
            list($key, $value) = explode(': ', $line);
            $headers[$key] = $value;
        }
        return $headers;
    }

    protected function parseRequestHeaders($headers)
    {
        $data = [];
        foreach ($headers as $k => $v) {
            $data[] = sprintf('%s: %s', $k, $v);
        }
        return $data;
    }

    protected function retrieve($url)
    {
        $this->debugInit();

        $this->curl = curl_init();
        curl_setopt_array(
            $this->curl,
            [
                CURLOPT_RETURNTRANSFER => true, /* return instead of outputting */
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true, /* include the header in the output */
                CURLOPT_FOLLOWLOCATION => true /* follow redirects */
            ]
        );
        if (!empty($this->requestHeaders)) {
            curl_setopt(
                $this->curl,
                CURLOPT_HTTPHEADER,
                $this->parseRequestHeaders($this->requestHeaders)
            );
        }

        $this->debugDo();

        if ($this->method == Http::METHOD_POST) {
            curl_setopt($this->curl, CURLOPT_POST, true);
            if (!empty($this->postData)) {
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postData);
            }
        }

        $this->response = curl_exec($this->curl);
        if (false === $this->response) {
            throw new ApplicationException(curl_error($this->curl));
        }

        $this->debugInfo = curl_getinfo($this->curl);

        $httpCode = $this->getHttpCode();

        curl_close($this->curl);

        /**
         * For redirects, the response will contain evey header/body pair.
         * The last header/body will be at the end of the response.
         */
        $responseParts = explode("\r\n\r\n", $this->response);
        $body = array_pop($responseParts);
        $headerString = array_pop($responseParts);

        $body = trim($body);
        $headers = $this->parseResponseHeaders($headerString);

        $this->debugFinish();

        return new \WebServCo\Framework\HttpResponse(
            $body,
            $httpCode,
            $headers
        );
    }
}
