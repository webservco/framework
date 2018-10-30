<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Framework;

trait RequestServerTrait
{
    public function getAcceptContentTypes()
    {
        if (!isset($this->server['HTTP_ACCEPT'])) {
            return false;
        }
        $acceptTypes = [];
        $httpAccept = strtolower(str_replace(' ', '', $this->server['HTTP_ACCEPT']));
        $parts = explode(',', $httpAccept);

        foreach ($parts as $item) {
            $q = 1; // the default quality is 1.
            if (strpos($item, ';q=')) { // check if there is a different quality
                // divide "mime/type;q=X" into two parts: "mime/type" i "X"
                list($item, $q) = explode(';q=', $item);
            }
            // WARNING: $q == 0 means, that mime-type isn’t supported!
            $acceptTypes[$q] = $item;
        }
        asort($acceptTypes);
        return $acceptTypes;
    }

    public function getAcceptLanguage()
    {
        if (!isset($this->server['HTTP_ACCEPT_LANGUAGE'])) {
            return false;
        }
        return substr($this->server['HTTP_ACCEPT_LANGUAGE'], 0, 2);
    }

    public function getContentType()
    {
        if (!isset($this->server['CONTENT_TYPE'])) {
            return false;
        }
        return $this->server['CONTENT_TYPE'];
    }

    public function getHost()
    {
        if (!empty($this->server['HTTP_HOST'])) {
            return $this->server['HTTP_HOST'];
        } elseif (!empty($this->server['SERVER_NAME'])) {
            return $this->server['SERVER_NAME'];
        } elseif (!empty($this->server['HOSTNAME'])) {
            return $this->server['HOSTNAME']; //CLI
        }
        return '';
    }

    public function getHostExtension()
    {
        $host = $this->getHost();
        if (empty($host)) {
            return false;
        }

        $parts = explode('.', $host);
        return end($parts);
    }

    public function getReferer()
    {
        return isset($this->server['HTTP_REFERER']) ? $this->server['HTTP_REFERER'] : null;
    }

    public function getSchema()
    {
        if (Framework::isCLI()) {
            return null;
        }

        if (isset($this->server['HTTPS']) && 'off' != $this->server['HTTPS']) {
            return 'https';
        } elseif (isset($this->server['HTTP_X_FORWARDED_PROTO']) &&
        'https' == $this->server['HTTP_X_FORWARDED_PROTO']) {
            return 'https';
        } elseif (isset($this->server['HTTP_X_FORWARDED_SSL']) &&
        'on' == $this->server['HTTP_X_FORWARDED_SSL']) {
            return 'https';
        }
        return 'http';
    }

    public function getServerProtocol()
    {
        if (!isset($this->server['SERVER_PROTOCOL'])) {
            return false;
        }
        return $this->server['SERVER_PROTOCOL'];
    }

    public function getRemoteAddress()
    {
        if (Framework::isCLI()) {
            return gethostbyname(php_uname('n'));
        }
        return isset($this->server['REMOTE_ADDR']) ? $this->server['REMOTE_ADDR'] : false;
    }

    public function getServerVariable($index)
    {
        if (!array_key_exists($index, $this->server)) {
            throw new \OutOfBoundsException('Requested key does not exist');
        }
        return $this->server[$index];
    }
}
