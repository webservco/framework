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
    
    final public function __construct($config, $server, $post = [])
    {
        parent::__construct($config);
        
        $this->init($server, $post);
    }
    
    final private function init($server, $post = [])
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
                $this->porcessPost($post);
                break;
        }
        if ($this->setting('clear_globals', true)) {
            $this->clearGlobals();
        }
    }
    
    final private function clearGlobals()
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
    
    final private function porcessPost($post = [])
    {
        $this->data = [];
        foreach ($post as $k => $v) {
            $this->data[$this->sanitize($k)] = $this->sanitize($v, false);
        }
        return true;
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
        list ($target, $queryString) = $this->parse($string);
        $this->target = $this->sanitize(urldecode($target));
        $this->query = $this->format($this->sanitize($queryString));
    }
    
    final private function getMethod()
    {
        return !empty($this->server['REQUEST_METHOD']) &&
        in_array(
            $this->server['REQUEST_METHOD'],
            \WebServCo\Framework\Http::getMethods()
        ) ?
        $this->server['REQUEST_METHOD'] : false;
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
    
    final public function sanitize($string, $extended = true)
    {
        $string = filter_var($string, FILTER_SANITIZE_URL);
        
        /**
         * Extra sanitization, eg. for _GET, _SERVER
         */
        if ($extended) {
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
        }
        
        return $string;
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
        list($target, $query) = $this->explode($string);
        $target = $this->removeSuffix($this->transform($target));
        $query = $this->transform($query);
        return [$target, $query];
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
        $parts = array_map('urldecode', $parts);
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
    
    final public function guessAppUrl()
    {
        if (\WebServCo\Framework\Framework::isCLI()) {
            return false;
        }
        return $this->getSchema() .
        '://' .
        $this->getHost() . 
        $this->path;
    }
}
