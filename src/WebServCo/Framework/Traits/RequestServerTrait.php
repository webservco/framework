<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Helpers\PhpHelper;

trait RequestServerTrait
{
    /**
    * @return array<string,string>
    */
    public function getAcceptContentTypes(): array
    {
        if (!isset($this->server['HTTP_ACCEPT'])) {
            return [];
        }
        $acceptTypes = [];
        $httpAccept = \strtolower(\str_replace(' ', '', $this->server['HTTP_ACCEPT']));
        $parts = \explode(',', $httpAccept);

        foreach ($parts as $item) {
            $q = 1; // the default quality is 1.
            if (\strpos($item, ';q=')) { // check if there is a different quality
                // divide "mime/type;q=X" into two parts: "mime/type" i "X"
                [$item, $q] = \explode(';q=', $item);
            }
            // Make sure key is string, otherwise it is cast by PHP to int and possibly overwritten.
            // WARNING: $q == 0 means, that mime-type isn’t supported!
            $acceptTypes[\sprintf('q=%s', $q)] = $item;
        }
        \asort($acceptTypes);
        return $acceptTypes;
    }

    public function getAcceptLanguage(): string
    {
        if (!isset($this->server['HTTP_ACCEPT_LANGUAGE'])) {
            return '';
        }
        return \substr($this->server['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    public function getContentType(): string
    {
        if (!isset($this->server['CONTENT_TYPE'])) {
            return '';
        }
        return $this->server['CONTENT_TYPE'];
    }

    public function getHost(): string
    {
        if (!empty($this->server['HTTP_HOST'])) {
            return $this->server['HTTP_HOST'];
        }

        if (!empty($this->server['SERVER_NAME'])) {
            return $this->server['SERVER_NAME'];
        }

        if (!empty($this->server['HOSTNAME'])) {
            return $this->server['HOSTNAME']; //CLI
        }
        return '';
    }

    public function getHostExtension(): string
    {
        $host = $this->getHost();
        if (empty($host)) {
            return '';
        }

        $parts = \explode('.', $host);
        return \end($parts);
    }

    public function getReferer(): string
    {
        return $this->server['HTTP_REFERER'] ?? '';
    }

    /**
    * Get remote address from current Request object.
    *
    * For a general helper method see \WebServCo\Framework\Helpers\RequestHelper.getRemoteAddress
    */
    public function getRemoteAddress(): string
    {
        if (PhpHelper::isCli()) {
            return \gethostbyname(\php_uname('n'));
        }

        return $this->server['REMOTE_ADDR'] ?? '';
    }

    public function getRefererHost(): string
    {
        return (string) \parse_url($this->getReferer(), \PHP_URL_HOST);
    }

    public function getSchema(): string
    {
        if (PhpHelper::isCli()) {
            return '';
        }

        if (isset($this->server['HTTPS']) && 'off' !== $this->server['HTTPS']) {
            return 'https';
        }

        if (isset($this->server['HTTP_X_FORWARDED_PROTO']) && 'https' === $this->server['HTTP_X_FORWARDED_PROTO']) {
            return 'https';
        }

        if (isset($this->server['HTTP_X_FORWARDED_SSL']) && 'on' === $this->server['HTTP_X_FORWARDED_SSL']) {
            return 'https';
        }
        return 'http';
    }

    public function getServerProtocol(): string
    {
        if (!isset($this->server['SERVER_PROTOCOL'])) {
            return '';
        }
        return $this->server['SERVER_PROTOCOL'];
    }

    public function getServerVariable(string $index): string
    {
        if (!\array_key_exists($index, $this->server)) {
            throw new \OutOfBoundsException('Requested key does not exist.');
        }
        return $this->server[$index];
    }

    public function getUserAgent(): string
    {
        if (PhpHelper::isCli()) {
            return '';
        }
        return $this->server['HTTP_USER_AGENT'] ?? '';
    }
}
