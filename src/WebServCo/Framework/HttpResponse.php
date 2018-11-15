<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Http;

class HttpResponse extends \WebServCo\Framework\AbstractResponse implements
    \WebServCo\Framework\Interfaces\ResponseInterface
{
    protected $statusText;
    protected $headers = [];
    protected $appendCharset;
    protected $charset;

    public function __construct($content = null, $statusCode = 200, array $headers = [])
    {
        $this->appendCharset = false;
        $this->setStatus($statusCode);

        $this->charset = 'utf-8';

        foreach ($headers as $name => $value) {
            $this->setHeader($name, $value);
        }

        $this->setContent($content);
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function setStatus($statusCode)
    {
        if (!isset(Http::$statusCodes[$statusCode])) {
                throw new \WebServCo\Framework\Exceptions\ApplicationException(
                    sprintf('Invalid HTTP status code: %s', $statusCode)
                );
        }
        $this->statusCode = $statusCode;
        $this->statusText = Http::$statusCodes[$statusCode];
    }

    public function setHeader($name, $value)
    {
        if ($this->appendCharset) {
            switch ($name) {
                case 'Content-Type':
                    $value .= '; charset=' . $this->charset;
                    break;
            }
        }
        $this->headers[$name] = $value;
    }

    public function send()
    {
        $this->sendHeaders();
        $this->sendContent();
        return $this->statusCode;
    }

    protected function sendHeader($name, $value, $statusCode)
    {
        header(
            sprintf('%s: %s', $name, $value),
            true,
            $statusCode
        );
        return true;
    }

    protected function sendHeaders()
    {
        foreach ($this->headers as $name => $value) {
            if (is_array($value)) {
                foreach ($value as $item) {
                    $this->sendHeader($name, $item, $this->statusCode);
                }
            } else {
                $this->sendHeader($name, $value, $this->statusCode);
            }
        }

        $this->sendHeader('Content-length', strlen($this->content), $this->statusCode);

        header(
            sprintf('HTTP/1.1 %s %s', $this->statusCode, $this->statusText),
            true,
            $this->statusCode
        );
    }

    protected function sendContent()
    {
        echo $this->content;
    }
}
