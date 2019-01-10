<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Http\Method;
use WebServCo\Framework\Exceptions\ApplicationException;

final class CurlBrowser implements
    \WebServCo\Framework\Interfaces\HttpBrowserInterface
{
    protected $debug;
    protected $skipSslVerification;
    protected $requestHeaders;

    protected $method;
    protected $postData;

    protected $curl;
    protected $debugStderr;
    protected $debugOutput;
    protected $debugInfo;
    protected $response;
    protected $responseHeaders;

    protected $logger;

    protected $curlError;

    public function __construct(\WebServCo\Framework\Interfaces\LoggerInterface $logger)
    {
        $this->logger = $logger;
        $this->debug = false;
        $this->skipSslVerification = false;
        $this->requestHeaders = [];
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

    public function post($url, $postData = null)
    {
        $this->setMethod(Method::POST);
        $this->setPostData($postData);
        return $this->retrieve($url);
    }

    public function retrieve($url)
    {
        $this->debugInit();


        $this->curl = curl_init();
        if (!is_resource($this->curl)) {
            throw new ApplicationException('Not a valid resource');
        }
        curl_setopt_array(
            $this->curl,
            [
                CURLOPT_RETURNTRANSFER => true, /* return instead of outputting */
                CURLOPT_URL => $url,
                CURLOPT_HEADER => true, /* include the header in the output */
                CURLOPT_FOLLOWLOCATION => true, /* follow redirects */
                CURLOPT_CONNECTTIMEOUT => 60, // The number of seconds to wait while trying to connect.
                CURLOPT_TIMEOUT => 60, // The maximum number of seconds to allow cURL functions to execute.
            ]
        );
        if ($this->skipSslVerification) {
            // stop cURL from verifying the peer's certificate
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);
            // don't check the existence of a common name in the SSL peer certificate
            curl_setopt($this->curl, CURLOPT_SSL_VERIFYHOST, 0);
        }

        $this->debugDo();

        switch ($this->method) {
            case Method::POST:
                curl_setopt($this->curl, CURLOPT_POST, true);
                if (!empty($this->postData)) {
                    curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->postData);
                    if (!is_array($this->postData)) {
                        $this->setRequestHeader('Content-Length', mb_strlen($this->postData));
                    }
                }
                break;
            case Method::HEAD:
                curl_setopt($this->curl, CURLOPT_NOBODY, true);
                break;
        }

        if (!empty($this->requestHeaders)) {
            curl_setopt(
                $this->curl,
                CURLOPT_HTTPHEADER,
                $this->parseRequestHeaders($this->requestHeaders)
            );
        }

        $this->response = curl_exec($this->curl);
        $this->curlError = curl_error($this->curl);
        if (false === $this->response) {
            throw new ApplicationException(sprintf("cURL error: %s", $this->curlError));
        }

        $this->debugInfo = curl_getinfo($this->curl);

        curl_close($this->curl);

        $httpCode = $this->getHttpCode();

        if (empty($httpCode)) {
            throw new ApplicationException(sprintf("Empty HTTP status code. cURL error: %s", $this->curlError));
        }

        /**
         * For redirects, the response will contain evey header/body pair.
         * The last header/body will be at the end of the response.
         */
        $responseParts = explode("\r\n\r\n", (string) $this->response);
        $body = trim((string) array_pop($responseParts));

        $this->responseHeaders = [];
        foreach ($responseParts as $item) {
            $this->responseHeaders[] = $this->parseResponseHeaders($item);
        }

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

    public function setPostData($postData)
    {
        if (is_array($postData)) {
            $this->postData = [];
            foreach ($postData as $key => $value) {
                if (is_array($value)) {
                    throw new \InvalidArgumentException('POST value can not be an array');
                }
                $this->postData[$key] = $value;
            }
            return true;
        }
        $this->postData = $postData;
        return true;
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
                $headers[$key][] = $value;
                $headers[$key] = array_values((array) $headers[$key]); // re-index array
            } else {
                $headers[$key] = $value;
            }
        }
        return $headers;
    }
}
