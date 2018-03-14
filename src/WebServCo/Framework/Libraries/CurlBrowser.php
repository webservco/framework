<?php
namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\Http;
use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Interfaces\LoggerInterface;

final class CurlBrowser extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\HttpBrowserInterface
{
    protected $method;

    protected $curl;
    protected $debugStderr;
    protected $debugOutput;
    protected $debugInfo;
    protected $response;

    protected $loggerInterface;

    public function setLogger(LoggerInterface $loggerInterface)
    {
        $this->loggerInterface = $loggerInterface;
    }

    public function get($url)
    {
        $this->setMethod(Http::METHOD_GET);
        return $this->retrieve($url);
    }

    public function post($url, $data = [])
    {
        $this->setMethod(Http::METHOD_POST);
        $this->setData('post', $data);
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

    protected function debugInit()
    {
        if ($this->setting('debug', true)) {
            ob_start();
            $this->debugStderr = fopen('php://output', 'w');
            return true;
        }
        return false;
    }

    protected function debugDo()
    {
        if ($this->setting('debug', true)) {
            //curl_setopt($this->curl, CURLINFO_HEADER_OUT, 1); /* verbose not working if this is enabled */
            curl_setopt($this->curl, CURLOPT_VERBOSE, 1);
            curl_setopt($this->curl, CURLOPT_STDERR, $this->debugStderr);
            return false;
        }
        return false;
    }

    protected function debugFinish()
    {
        if ($this->setting('debug', true)) {
            fclose($this->debugStderr);
            $this->debugOutput = ob_get_clean();

            if (!($this->loggerInterface instanceof LoggerInterface)) {
                throw new ApplicationException('No LoggerInterface specified');
            }

            $this->loggerInterface->debug('CURL INFO:', $this->debugInfo);
            $this->loggerInterface->debug('CURL VERBOSE:', $this->debugOutput);
            $this->loggerInterface->debug('CURL RESPONSE:', $this->response);

            return true;
        }
        return false;
    }

    protected function getHttpCode()
    {
        return isset($this->debugInfo['http_code']) ? $this->debugInfo['http_code']: false;
    }

    protected function parseHeaders($headerString)
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
        if ($this->setting('userAgent', false)) {
            curl_setopt($this->curl, CURLOPT_USERAGENT, $this->setting('userAgent'));
        }

        $this->debugDo();

        if ($this->method == Http::METHOD_POST) {
            curl_setopt($this->curl, CURLOPT_POST, true);
            if ($this->data('post', [])) {
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $this->data('post', []));
            }
        }

        $this->response = curl_exec($this->curl);
        if (false === $this->response) {
            throw new ApplicationException(curl_error($this->curl));
        }

        $this->debugInfo = curl_getinfo($this->curl);

        $httpCode = $this->getHttpCode();

        curl_close($this->curl);

        list($headerString, $body) = explode("\r\n\r\n", $this->response, 2);

        $body = trim($body);
        $headers = $this->parseHeaders($headerString);

        $this->debugFinish();

        return new \WebServCo\Framework\HttpResponse(
            $body,
            $httpCode,
            $headers
        );
    }
}
