<?php
namespace WebServCo\Framework\Http;

class Response extends \WebServCo\Framework\AbstractResponse implements
    \WebServCo\Framework\Interfaces\ResponseInterface
{
    protected string $statusText;

    /**
    * @var array<string,array<string,string>|string>
    */
    protected array $headers;
    protected bool $appendCharset;
    protected string $charset;

    /**
    * @param string $content
    * @param int $statusCode
    * @param array<string,array<string,string>|string> $headers
    */
    public function __construct(string $content, int $statusCode = 200, array $headers = [])
    {
        $this->appendCharset = false;
        $this->setStatus($statusCode);

        $this->charset = 'utf-8';

        $this->headers = [];

        foreach ($headers as $name => $value) {
            /*
            Make sure header names are lower case, to prevent inconsistency problems.
            https://www.w3.org/Protocols/rfc2616/rfc2616-sec4.html#sec4.2
            */
            $this->setHeader(strtolower($name), $value);
        }

        $this->setContent($content);
    }

    /**
    * @param string $name
    * @return array<string,string>|string|null
    */
    public function getHeader(string $name)
    {
        $name = strtolower($name);
        if (array_key_exists($name, $this->headers)) {
            return $this->headers[$name];
        }
        return null;
    }

    /**
    * @return array<string,array<string,string>|string>
    */
    public function getHeaders() : array
    {
        return $this->headers;
    }

    public function setStatus(int $statusCode) : bool
    {
        $statusCodes = StatusCode::getSupported();
        if (!isset($statusCodes[$statusCode])) {
                throw new \WebServCo\Framework\Exceptions\ApplicationException(
                    sprintf('Invalid HTTP status code: %s.', $statusCode)
                );
        }
        $this->statusCode = $statusCode;
        $this->statusText = $statusCodes[$statusCode];
        return true;
    }

    /**
    * @param string $name
    * @param array<string,string>|string $value
    * @return bool
    */
    public function setHeader(string $name, $value) : bool
    {
        if ($this->appendCharset) {
            switch ($name) {
                case 'Content-Type':
                    if (is_string($value)) {
                        $value .= '; charset=' . $this->charset;
                    }
                    break;
            }
        }
        $this->headers[$name] = $value;
        return true;
    }

    public function send() : int
    {
        $this->sendHeaders();
        $this->sendContent();
        return $this->statusCode;
    }

    protected function sendHeader(string $name, string $value, int $statusCode) : bool
    {
        header(
            sprintf('%s: %s', $name, $value),
            true,
            $statusCode
        );
        return true;
    }

    protected function sendHeaders() : bool
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
        // use strlen and not mb_strlen: "The length of the request body in octets (8-bit bytes)."
        $this->sendHeader('Content-length', (string) strlen($this->content), $this->statusCode);

        header(
            sprintf('HTTP/1.1 %s %s', $this->statusCode, $this->statusText),
            true,
            $this->statusCode
        );
        return true;
    }

    protected function sendContent() : bool
    {
        echo $this->content;
        return true;
    }
}
