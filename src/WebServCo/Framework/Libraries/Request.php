<?php
namespace WebServCo\Framework\Libraries;

final class Request extends \WebServCo\Framework\AbstractLibrary
{
    private $server = [];
    public $path;
    public $filename;
    public $custom;
    public $query;
    
    final public function __construct($config, $server)
    {
        parent::__construct($config);
        
        $this->server = $server;
        $this->filename = $this->getFilename();
        $this->path = $this->getPath();
        
        $this->process();
    }
    
    final private function getFilename()
    {
        return !empty($this->server['SCRIPT_NAME']) ?
        basename($this->server['SCRIPT_NAME']) : false;
    }
    
    final private function getPath()
    {
        return !empty($this->server['SCRIPT_NAME']) ?
        str_replace($this->getFilename(), '', $this->server['SCRIPT_NAME']) :
        false;
    }
    
    final private function process()
    {
        switch (true) {
            case isset($this->server['REQUEST_URI']):
                $string = $this->server['REQUEST_URI'];
                break;
            case isset($this->server['PATH_INFO']):
                $string = $this->server['PATH_INFO'];
                break;
            case isset($this->server['ORIG_PATH_INFO']):
                $string = $this->server['ORIG_PATH_INFO'];
                break;
            case !empty($this->server['QUERY_STRING']):
                $string = $this->server['ORIG_PATH_INFO'];
                break;
            default:
                return false; //CLI
                break;
        }
        list ($this->custom, $queryString) = $this->parse($string);
        $this->query = $this->format($queryString);
    }
    
    final private function parse($string)
    {
        $pathLen = strlen($this->path);
        if (0 === strncasecmp($this->path, $string, $pathLen)) {
            $string = substr($string, $pathLen);
        }
        $filenameLen = strlen($this->filename);
        if (0 === strncasecmp($this->filename, $string, $filenameLen)) {
            $string = substr($string, $filenameLen);
        }
        list($custom, $query) = $this->explode($string);
        $custom = $this->removeSuffix($this->transform($custom));
        $query = $this->transform($query);
        return [$custom, $query];
    }
    
    final private function explode($string)
    {
        if (false !== strpos($string, '?')) {
            return explode('?', $string, 2);
        } elseif (false !== strpos($string, '&')) {
            return explode('&', $string, 2);
        }
        return [$string, null];
    }
    
    final private function transform($string)
    {
        $string = str_replace(['?','&','=','//'], ['','/','/','/0/'], $string);
        return trim($string, ' /');
    }
    
    final private function removeSuffix($string)
    {
        $suffixes = $this->setting('suffixes');
        if (is_array($suffixes)) {
            $stringRev = strrev($string);
            foreach ($suffixes as $suffix) {
                $suffixRev = strrev($suffix);
                $suffixLen = strlen($suffix);
                if (0 === strncasecmp($suffixRev, $stringRev, $suffixLen)) {
                    return strrev(substr($stringRev, $suffixLen));
                }
            }
        }
        return $string;
    }
    
    final private function format($string)
    {
        $data = [];
        $parts = $this->split($string);
        $num = count($parts);
        for ($position = 0; $position < $num; $position +=2) {
            $data[$parts[$position]] = $position == $num -1 ? null :
            $parts[$position + 1];
        }
        return $data;
    }
    
    final public function split($string)
    {
        $parts = explode('/', $string);
        return array_diff($parts, ['']);
    }
    
    final public function getSchema()
    {
        if (\WebServCo\Framework\Framework::isCLI()) {
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
    
    final public function getReferer()
    {
        return isset($this->server['HTTP_REFERER']) ? $this->server['HTTP_REFERER'] : null;
    }
    
    final public function getHost()
    {
        if (!empty($this->server['HTTP_HOST'])) {
            return $this->server['HTTP_HOST'];
        } elseif (!empty($this->server['SERVER_NAME'])) {
            return $this->server['SERVER_NAME'];
        } elseif (!empty($this->server['HOSTNAME'])) {
            return $this->server['HOSTNAME']; //CLI
        }
        return null;
    }
}
