<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Http\Method;

trait RequestProcessTrait
{
    abstract public function clearData(): bool;

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     * @return bool True on success and false on failure.
     */
    abstract public function setData($key, $value): bool;

    /**
    * @param mixed $key Can be an array, a string,
    *                          or a special formatted string
    *                          (eg 'i18n/lang').
    * @param mixed $defaultValue
    * @return mixed
    */
    abstract public function setting($key, $defaultValue = null);

    /**
    * Data value can be another array, for example _SERVER in CLI ("argv" is an array)
    * Important: only first level is sanitized.
    *
    * @param array<string,array<int,string>|string> $data
    * @return array<string,array<int,string>|string>
    */
    public function sanitize(array $data): array
    {
        foreach ($data as $key => $value) {
            if (\is_string($value)) {
                // Sanitize only first level (prevents "argv" from being sanitized)
                $value = \WebServCo\Framework\Helpers\RequestHelper::sanitizeString($value);
            }
            $data[$key] = $value;
        }
        return $data;
    }

    /**
    * @param array<string,mixed> $server
    * @param array<string,string> $post
    */
    protected function init(array $server, array $post = []): bool
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
        if ($this->setting('clear_globals', false)) {
            $this->clearGlobals();
        }
        return true;
    }

    protected function setBody(): bool
    {
        $this->body = (string) \file_get_contents('php://input');
        return true;
    }

    protected function setMethod(): bool
    {
        if (
            empty($this->server['REQUEST_METHOD']) ||
            !\in_array(
                $this->server['REQUEST_METHOD'],
                Method::getSupported(),
                true,
            )
        ) {
            return false;
        }
        $this->method = $this->server['REQUEST_METHOD'];
        return true;
    }

    protected function setFilename(): bool
    {
        if (empty($this->server['SCRIPT_NAME'])) {
            return false;
        }
        $this->filename = \basename($this->server['SCRIPT_NAME']);
        return true;
    }

    protected function setPath(): bool
    {
        if (empty($this->server['SCRIPT_NAME'])) {
            return false;
        }

        $this->path = \rtrim(
            \str_replace(
                $this->filename,
                '',
                $this->server['SCRIPT_NAME'],
            ),
            \DIRECTORY_SEPARATOR,
        );

        return true;
    }

    protected function clearGlobals(): bool
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

    /**
    * @param array<string,string> $post
    */
    protected function processPost(array $post = []): bool
    {
        $this->clearData();
        foreach ($post as $k => $v) {
            $this->setData(\WebServCo\Framework\Helpers\RequestHelper::sanitizeString($k), $v);
        }
        return true;
    }

    protected function process(): bool
    {
        if (\WebServCo\Framework\Helpers\PhpHelper::isCli()) {
            return $this->processCli();
        }

        return $this->processHttp();
    }

    protected function processCli(): bool
    {
        if (isset($this->server['argv'][1])) {
            $this->target = $this->server['argv'][1];
        }
        if (isset($this->server['argv'][2])) {
            foreach ($this->server['argv'] as $k => $v) {
                if (\in_array($k, [0, 1], true)) {
                    continue;
                }
                $this->args[] = \WebServCo\Framework\Helpers\RequestHelper::sanitizeString($v);
            }
        }
        return true;
    }

    protected function processHttp(): bool
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
        [$target, $queryString, $suffix] = \WebServCo\Framework\Helpers\RequestHelper::parse(
            $string,
            $this->path,
            $this->filename,
            $this->setting('suffixes', []),
        );
        $this->target = \WebServCo\Framework\Helpers\RequestHelper::sanitizeString(\urldecode($target));
        $this->query = \WebServCo\Framework\Helpers\RequestHelper::format(
            \WebServCo\Framework\Helpers\RequestHelper::sanitizeString($queryString),
        );
        $this->suffix = $suffix;
        return true;
    }
}
