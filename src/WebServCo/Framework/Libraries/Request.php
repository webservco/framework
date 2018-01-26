<?php
namespace WebServCo\Framework\Libraries;

final class Request extends \WebServCo\Framework\AbstractLibrary
{
    /**
     * Sanitized _SERVER data.
     */
    public $server = [];
    /**
     * Request method.
     */
    public $method;
    /**
     * Current script filename. Should most commonly be index.php
     */
    public $filename;
    /**
     * Script path.
     * For HTTP requests this will be public web server subdirectory
     * the project is located in.
     * For CLI request this will be the script path
     * (full or relative, depending on how the script was called).
     */
    public $path = '';
    /**
     * Sanitized Framework customized target path.
     */
    public $target = '';
    /**
     * Sanitized request query.
     */
    public $query = [];
    
    public function __construct($config, $server, $post = [])
    {
        parent::__construct($config);
        
        $this->init($server, $post);
    }
    
    private function init($server, $post = [])
    {
        $this->server = array_map([$this, 'sanitize'], $server);
        $this->method = $this->getMethod();
        $this->filename = $this->getFilename();
        $this->path = $this->getPath();
        $this->process();
        
        switch ($this->method) {
            case \WebServCo\Framework\Http::METHOD_GET:
            case \WebServCo\Framework\Http::METHOD_HEAD:
                break;
            case \WebServCo\Framework\Http::METHOD_POST:
                $this->processPost($post);
                break;
        }
        if ($this->setting('clear_globals', true)) {
            $this->clearGlobals();
        }
    }
    
    private function clearGlobals()
    {
        if (!empty($_GET)) {
            foreach ($_GET as $k => $v) {
                unset($_REQUEST[$k]);
            }
            $_GET = [];
        }
        if (!empty($_POST)) {
            $_POST = [];
        }
        return true;
    }
    
    private function processPost($post = [])
    {
        $this->data = [];
        foreach ($post as $k => $v) {
            $this->data[$this->sanitize($k)] = $v;
        }
        return true;
    }
    
    private function process()
    {
        $string = null;
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
                break;
        }
        if (empty($string)) {
            return false; //CLI
        }
        list ($target, $queryString) = $this->parse($string);
        $this->target = $this->sanitize(urldecode($target));
        $this->query = $this->format($this->sanitize($queryString));
    }
    
    private function getMethod()
    {
        return !empty($this->server['REQUEST_METHOD']) &&
        in_array(
            $this->server['REQUEST_METHOD'],
            \WebServCo\Framework\Http::getMethods()
        ) ?
        $this->server['REQUEST_METHOD'] : false;
    }
    
    private function getFilename()
    {
        return !empty($this->server['SCRIPT_NAME']) ?
        basename($this->server['SCRIPT_NAME']) : false;
    }
    
    private function getPath()
    {
        if (empty($this->server['SCRIPT_NAME'])) {
            return false;
        }
        $path = str_replace($this->getFilename(), '', $this->server['SCRIPT_NAME']);
        return rtrim($path, DIRECTORY_SEPARATOR);
    }
    
    public function sanitize($string)
    {
        // Strip tags, optionally strip or encode special characters.
        $string = filter_var($string, FILTER_SANITIZE_STRING);
        $unwanted = [
            "`",
            //"'",
            //'"',
            "\b",
            "\n",
            "\r",
            "\t",
            //"?",
            //"!",
            //"~",
            //"#",
            //"^",
            //"&",
            //"*",
            //"=",
            //"[",
            //"]",
            //":",
            //";",
            //",",
            //"|",
            "\\",
            //"{",
            //"}",
            //"(",
            //")",
            "\$"
        ];
        $string = str_replace($unwanted, '', $string);
        return $string;
    }
    
    private function parse($string)
    {
        $pathLen = strlen($this->path);
        if (0 === strncasecmp($this->path, $string, $pathLen)) {
            $string = substr($string, $pathLen);
        }
        $filenameLen = strlen($this->filename);
        if (0 === strncasecmp($this->filename, $string, $filenameLen)) {
            $string = substr($string, $filenameLen);
        }
        list($target, $query) = $this->explode($string);
        $target = $this->removeSuffix($this->transform($target));
        $query = $this->transform($query);
        return [$target, $query];
    }
    
    private function explode($string)
    {
        if (false !== strpos($string, '?')) {
            return explode('?', $string, 2);
        } elseif (false !== strpos($string, '&')) {
            return explode('&', $string, 2);
        }
        return [$string, null];
    }
    
    private function transform($string)
    {
        $string = str_replace(['?','&','=','//'], ['','/','/','/0/'], $string);
        return trim($string, ' /');
    }
    
    private function removeSuffix($string)
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
    
    private function format($string)
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
    
    public function split($string)
    {
        $parts = explode('/', $string);
        $parts = array_map('urldecode', $parts);
        return array_diff($parts, ['']);
    }
    
    public function getSchema()
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
    
    public function getReferer()
    {
        return isset($this->server['HTTP_REFERER']) ? $this->server['HTTP_REFERER'] : null;
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
        return null;
    }
    
    public function guessAppUrl()
    {
        if (\WebServCo\Framework\Framework::isCLI()) {
            return false;
        }
        return $this->getSchema() .
        '://' .
        $this->getHost() .
        $this->path .
        DIRECTORY_SEPARATOR;
    }
}
