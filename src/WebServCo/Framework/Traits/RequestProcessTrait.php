<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Http\Method;
use WebServCo\Framework\RequestUtils;

trait RequestProcessTrait
{
    abstract public function setting($key, $defaultValue = false);

    public function sanitize($data)
    {
        if (is_array($data)) {
            array_walk_recursive(
                $data,
                '\WebServCo\Framework\RequestUtils::sanitizeString'
            );
            return $data;
        }
        return RequestUtils::sanitizeString($data);
    }

    protected function init($server, $post = [])
    {
        $this->server = $this->sanitize($server);

        $this->setBody();
        $this->setMethod();
        $this->setFilename();
        $this->setPath();

        $this->process();

        switch ($this->method) {
            case Method::GET:
            case Method::HEAD:
                break;
            case Method::POST:
                $this->processPost($post);
                break;
        }
        if ($this->setting('clear_globals', true)) {
            $this->clearGlobals();
        }
    }

    protected function setBody()
    {
        $this->body = file_get_contents('php://input');
    }

    protected function setMethod()
    {
        if (empty($this->server['REQUEST_METHOD']) ||
        !in_array(
            $this->server['REQUEST_METHOD'],
            Method::getSupported()
        )) {
            return false;
        }
        $this->method = $this->server['REQUEST_METHOD'];
        return true;
    }

    protected function setFilename()
    {
        if (empty($this->server['SCRIPT_NAME'])) {
            return false;
        }
        $this->filename = basename($this->server['SCRIPT_NAME']);
        return true;
    }

    protected function setPath()
    {
        if (empty($this->server['SCRIPT_NAME'])) {
            return false;
        }

        $this->path = rtrim(
            str_replace(
                $this->filename,
                '',
                $this->server['SCRIPT_NAME']
            ),
            DIRECTORY_SEPARATOR
        );

        return true;
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
        if (\WebServCo\Framework\Framework::isCli()) {
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
        list ($target, $queryString, $suffix) = RequestUtils::parse(
            $string,
            $this->path,
            $this->filename,
            $this->setting('suffixes')
        );
        $this->target = $this->sanitize(urldecode($target));
        $this->query = RequestUtils::format($this->sanitize($queryString));
        $this->suffix = $suffix;
        return true;
    }
}
