<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Http;
use WebServCo\Framework\Exceptions\ApplicationException;

final class CurlBrowser implements
    \WebServCo\Framework\Interfaces\HttpBrowserInterface
{
    protected $debug = true;
    protected $skipSslVerification = false;
    protected $requestHeaders = [];

    protected $method;
    protected $postData;

    protected $curl;
    protected $debugStderr;
    protected $debugOutput;
    protected $debugInfo;
    protected $response;
    protected $responseHeaders;

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

    public function setSkipSSlVerification(bool $skipSslVerification)
    {
        $this->skipSslVerification = $skipSslVerification;
    }

    public function setRequestHeaders(array $requestHeaders)
    {
        $this->requestHeaders = $requestHeaders;
    }

    public function getResponseHeaders()
    {
        return $this->responseHeaders;
    }

    public function get($url)
    {
        $this->setMethod(Http::METHOD_GET);
        return $this->retrieve($url);
    }

    public function head($url)
    {
        $this->setMethod(Http::METHOD_HEAD);
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

    protected function parseResponseHeaders($headerString)
    {
        $headers = [];
        $lines = explode("\r\n", $headerString);
        foreach ($lines as $index => $line) {
            if (0 === $index) {
                continue; /* we'll get the status code elsewhere */
            }
            list($key, $value) = explode(': ', $line);
            if (isset($headers[$key])) {
                if (!is_array($headers[$key])) {
                    $headers[$key] = [$headers[$key]];
                }
                // check cookies
                if ('Set-Cookie' == $key) {
                    $parts = explode('=', $value, 2);
                    $cookieName = $parts[0];
                    foreach ($headers[$key] as $index => $existing) {
                        //check if we already have a cookie with the same name
                        if (0 === mb_stripos($existing, $cookieName)) {
                            // remove previous cookie with the same name
                            unset($headers[$key][$index]);
                        }
                    }
                }
                $headers[$key][] = $value;
                $headers[$key] = array_values($headers[$key]); // re-index array
            } else {
                $headers[$key] = $value;
            }
        }
        return $headers;
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
                CURLOPT_FOLLOWLOCATION => true, /* follow redirects */
            ]
        );
        if ($this->skipSslVerification) {
            // stop cURL from verifying the peer's certificate
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
            // don't check the existence of a common name in the SSL peer certificate
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        }
        if (!empty($this->requestHeaders)) {
            curl_setopt(
                $this->curl,
                CURLOPT_HTTPHEADER,
                $this->parseRequestHeaders($this->requestHeaders)
            );
        }

        $this->debugDo();

        switch ($this->method) {
            case Http::METHOD_POST:
                curl_setopt($this->curl, CURLOPT_POST, true);
                if (!empty($this->postData)) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postData);
                }
                break;
            case Http::METHOD_HEAD:
                curl_setopt($this->curl, CURLOPT_NOBODY, true);
                break;
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
        $body = trim(array_pop($responseParts));

        $this->responseHeaders = [];
        foreach ($responseParts as $item) {
            $this->responseHeaders[] = $this->parseResponseHeaders($item);
        }

        $this->debugFinish();

        return new \WebServCo\Framework\HttpResponse(
            $body,
            $httpCode,
            end($this->responseHeaders)
        );
    }
}
