<?php
namespace WebServCo\Framework;

use WebServCo\Framework\RequestUtils as Utils;

abstract class AbstractRequest extends \WebServCo\Framework\AbstractLibrary
{
    /**
     * Sanitized _SERVER data.
     */
    protected $server = [];
    /**
     * Request method.
     */
    protected $method;
    /**
     * Current script filename. Should most commonly be index.php
     */
    protected $filename;
    /**
     * Script path.
     * For HTTP requests this will be public web server subdirectory
     * the project is located in.
     * For CLI request this will be the script path
     * (full or relative, depending on how the script was called).
     */
    protected $path = '';
    /**
     * Sanitized Framework customized target path.
     */
    protected $target = '';
    /**
     * Sanitized request query.
     */
    protected $query = [];
    /**
     * Sanitized Framework customized CLI arguments.
     *
     * Excludes the script name and the second argument
     * which is the Framework customized target path.
     */
    protected $args = [];
    
    final public function sanitize($data)
    {
        if (is_array($data)) {
            array_walk_recursive(
                $data,
                'WebServCo\Framework\RequestUtils::sanitizeString'
            );
            return $data;
        }
        return Utils::sanitizeString($data);
    }
    
    protected function init($server, $post = [])
    {
        $this->server = $this->sanitize($server);
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
    
    protected function clearGlobals()
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
    
    protected function processPost($post = [])
    {
        $this->data = [];
        foreach ($post as $k => $v) {
            $this->data[$this->sanitize($k)] = $v;
        }
        return true;
    }
    
    protected function process()
    {
        if (\WebServCo\Framework\Framework::isCLI()) {
            return $this->processCli();
        }
        
        return $this->processHttp();
    }
    
    protected function processCli()
    {
        if (isset($this->server['argv'][1])) {
            $this->target = $this->server['argv'][1];
        }
        if (isset($this->server['argv'][2])) {
            foreach ($this->server['argv'] as $k => $v) {
                if (in_array($k, [0,1])) {
                    continue;
                }
                $this->args[] = $this->sanitize($v);
            }
        }
        return true;
    }
    
    protected function processHttp()
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
        list ($target, $queryString) = Utils::parse(
            $string,
            $this->path,
            $this->filename,
            $this->setting('suffixes')
        );
        $this->target = $this->sanitize(urldecode($target));
        $this->query = Utils::format($this->sanitize($queryString));
        return true;
    }
    
    protected function getMethod()
    {
        return !empty($this->server['REQUEST_METHOD']) &&
        in_array(
            $this->server['REQUEST_METHOD'],
            \WebServCo\Framework\Http::getMethods()
        ) ?
        $this->server['REQUEST_METHOD'] : false;
    }
    
    protected function getFilename()
    {
        return !empty($this->server['SCRIPT_NAME']) ?
        basename($this->server['SCRIPT_NAME']) : false;
    }
    
    protected function getPath()
    {
        if (empty($this->server['SCRIPT_NAME'])) {
            return false;
        }
        $path = str_replace($this->getFilename(), '', $this->server['SCRIPT_NAME']);
        return rtrim($path, DIRECTORY_SEPARATOR);
    }
}
