<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\EnvironmentConfiguration\Loader;

abstract class AbstractApplication
{

    protected string $projectNamespace;
    protected string $projectPath;

    abstract protected function config(): \WebServCo\Framework\Interfaces\ConfigInterface;

    abstract protected function request(): \WebServCo\Framework\Interfaces\RequestInterface;

    public function __construct(string $publicPath, string $projectPath, string $projectNamespace = 'Project')
    {
        $this->projectNamespace = $projectNamespace;
        $publicPath = \rtrim($publicPath, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;
        $this->projectPath = \rtrim($projectPath, \DIRECTORY_SEPARATOR) . \DIRECTORY_SEPARATOR;

        if (!\is_readable($publicPath . 'index.php')) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException('Public web path is not readable.');
        }

        Loader::set('APP_PATH_WEB', $publicPath);
        Loader::set('APP_PATH_PROJECT', $this->projectPath);
        Loader::set('APP_PATH_LOG', \sprintf('%svar/log/', $this->projectPath));
    }

    /**
     * Handle Errors.
     */
    final protected function handleErrors(?\Throwable $exception = null): bool
    {
        $errorInfo = \WebServCo\Framework\ErrorHandler::getErrorInfo($exception);
        if (!empty($errorInfo['message'])) {
            return $this->halt($errorInfo);
        }
        return false;
    }

    /**
    * @param array<string,mixed> $errorInfo
    */
    final protected function halt(array $errorInfo = []): bool
    {
        return \WebServCo\Framework\Helpers\PhpHelper::isCli()
            ? $this->haltCli($errorInfo)
            : $this->haltHttp($errorInfo);
    }

    /**
     * Handle HTTP errors.
     *
     * @param array<string,mixed> $errorInfo
     */
    protected function haltHttp(array $errorInfo = []): bool
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
                $title = 'Boo boo';
                break;
        }

        $output = '<!doctype html>
            <html>
            <head>
                <meta name="viewport" content="width=device-width, initial-scale=1.0">
                <title>Oops</title>
                <style>
                * {background: #f2dede; color: #a94442; overflow-wrap: break-word;}
                .i {margin-left: auto; margin-right: auto; text-align: center; width: auto;}
                small {font-size: 0.8em;}
                </style>
            </head>
            <body><div class="i"><br>' .
            "<h1>{$title}</h1>";
        if (Environment::DEVELOPMENT === ($_SERVER['APP_ENVIRONMENT'] ?? Environment::DEVELOPMENT)) {
            $output .= \sprintf(
                '<p><i>%s</i></p><p>%s:%s</p>',
                $errorInfo['message'],
                \basename($errorInfo['file']),
                $errorInfo['line'],
            );
            if (!empty($errorInfo['trace'])) {
                $output .= "<p>";
                $output .= "<small>";
                foreach ($errorInfo['trace'] as $item) {
                    if (!empty($item['class'])) {
                        $output .= \sprintf('%s%s', $item['class'], $item['type']);
                        $output .= "";
                    }
                    if (!empty($item['function'])) {
                        $output .= \sprintf('%s', $item['function']);
                        $output .= "";
                    }
                    if (!empty($item['file'])) {
                        $output .= \sprintf(
                            ' [%s:%s]',
                            \basename($item['file']),
                            $item['line'],
                        );
                        $output .= " ";
                    }
                    $output .= "<br>";
                }
                $output .= "</small></p>";
            }
        }
        $output .= '</div></body></html>';

        $response = new \WebServCo\Framework\Http\Response(
            $output,
            $statusCode,
            ['Content-Type' => ['text/html']],
        );
        $response->send();
        return true;
    }

    /**
    * @param array<string,mixed> $errorInfo
    */
    protected function haltCli(array $errorInfo = []): bool
    {
        $output = 'Boo boo' . \PHP_EOL;
        $output .= $errorInfo['message'] . \PHP_EOL;
        $output .= "$errorInfo[file]:$errorInfo[line]" . \PHP_EOL;
        $response = new \WebServCo\Framework\Cli\Response($output, 1);
        $response->send();
        return true;
    }

    protected function loadEnvironmentConfiguration(): bool
    {
        return \WebServCo\Framework\EnvironmentConfiguration\Loader::load($this->projectPath);
    }
}
