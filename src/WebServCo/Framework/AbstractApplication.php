<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Environment as Env;

abstract class AbstractApplication
{
    abstract protected function config();
    abstract protected function request();

    /**
     * Handle Errors.
     *
     * @param mixed $exception An \Error or \Exception object.
     */
    final protected function handleErrors($exception = null)
    {
        $errorInfo = [
            'code' => 0,
            'message' => null,
            'file' => null,
            'line' => null,
            'trace' => null,
        ];
        if ($exception instanceof \Throwable ||
            $exception instanceof \Exception
        ) {
            $errorInfo['code'] = $exception->getCode();
            $errorInfo['message'] = $exception->getMessage();
            $errorInfo['file'] = $exception->getFile();
            $errorInfo['line'] = $exception->getLine();
            $errorInfo['trace'] = $exception->getTraceAsString();
        } else {
            $last_error = error_get_last();
            if (!empty($last_error['message'])) {
                $errorInfo['message'] = $last_error['message'];
            }
            if (!empty($last_error['file'])) {
                $errorInfo['file'] = $last_error['file'];
            }
            if (!empty($last_error['line'])) {
                $errorInfo['line'] = $last_error['line'];
            }
        }
        if (!empty($errorInfo['message'])) {
            return $this->halt($errorInfo);
        }
        return false;
    }

    final protected function halt($errorInfo = [])
    {
        if (Fw::isCLI()) {
            return $this->haltCli($errorInfo);
        } else {
            return $this->haltHttp($errorInfo);
        }
    }

    protected function haltHttp($errorInfo = [])
    {
        switch ($errorInfo['code']) {
            case 404:
                $statusCode = 404; //not found
                $title = 'Resource not found';
                break;
            case 500: //application
            case 0: //default
            default:
                $statusCode = 500;
                $title = 'The App made a boo boo';
                break;
        }

        $output = '<!doctype html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Oups</title>
    <style>
    * {background: #f2dede; color: #a94442;}
    .o {display: table; position: absolute; height: 100%; width: 100%;}
    .m {display: table-cell; vertical-align: middle;}
    .i {margin-left: auto; margin-right: auto; width: 680px; text-align: center;}
    small {font-size: 0.7em;}
    </style>
</head>
<body><div class="o"><div class="m"><div class="i">' .
        "<h1>{$title}</h1>";
        if (Env::ENV_PROD !== $this->config()->getEnv()) {
            $output .= "<p>$errorInfo[message]</p>" .
            "<p><small>$errorInfo[file]:$errorInfo[line]</small></p>";
        }
        $output .= '</div></div></div></body></html>';

        $response = new \WebServCo\Framework\Libraries\HttpResponse(
            $output,
            $statusCode,
            ['Content-Type' => 'text/html']
        );
        $response->send($this->request());
        return true;
    }

    protected function haltCli($errorInfo = [])
    {
        $output = 'The App made a boo boo' . PHP_EOL;
        if (Env::ENV_PROD !== $this->config()->getEnv()) {
            $output .= $errorInfo['message'] . PHP_EOL;
            $output .= "$errorInfo[file]:$errorInfo[line]" . PHP_EOL;
        }
        $response = new \WebServCo\Framework\Libraries\CliResponse(
            $output,
            1
        );
        $response->send($this->request());
        return true;
    }
}
