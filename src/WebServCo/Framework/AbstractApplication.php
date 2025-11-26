<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use Throwable;
use WebServCo\Framework\Cli\Response;
use WebServCo\Framework\Environment\Setting;
use WebServCo\Framework\Environment\Settings;
use WebServCo\Framework\Exceptions\NonApplicationException;
use WebServCo\Framework\Helpers\ErrorObjectHelper;
use WebServCo\Framework\Helpers\PhpHelper;
use WebServCo\Framework\Http\Response as HttpResponse;
use WebServCo\Framework\Http\StatusCode;
use WebServCo\Framework\Interfaces\ResponseInterface;
use WebServCo\Framework\Traits\ExposeLibrariesTrait;

use function array_key_exists;
use function call_user_func_array;
use function class_exists;
use function get_parent_class;
use function is_callable;
use function is_readable;
use function method_exists;
use function realpath;
use function register_shutdown_function;
use function rtrim;
use function sprintf;

use const DIRECTORY_SEPARATOR;

abstract class AbstractApplication
{
    use ExposeLibrariesTrait;

    protected string $projectNamespace;
    protected string $projectPath;

    /**
     * Runs the application.
     */
    final public function run(): void
    {
        try {
            ErrorHandler::set();

            register_shutdown_function([$this, 'shutdown']);

            $this->loadEnvironmentSettings();

            $response = $this->getResponse();

            // Handle errors that happen before PHP script execution (so before the error handler is registered)
            // Call helper with no exception as parameter.
            // Helper will check for last error and return an \ErrorException object.
            // This code after `execute` and before `send` in order to give the app a chance to handle this situation.
            $throwable = ErrorObjectHelper::get(null);
            if ($throwable instanceof Throwable) {
                throw new NonApplicationException($throwable->getMessage(), $throwable->getCode(), $throwable);
            }

            $statusCode = $response->send();

            //$this->shutdown(null, true, PhpHelper::isCli() ? $statusCode : 0);
            $this->shutdown(null, true, $statusCode);
        } catch (Throwable $e) {
            $this->shutdown($e, true);
        }
    }

    /**
     * Finishes the execution of the Application.
     *
     * This method is also registered as a shutdown handler.
     */
    final public function shutdown(?Throwable $exception = null, bool $manual = false, int $statusCode = 0): void
    {
        $hasError = $this->handleErrors($exception);
        if ($hasError) {
            $statusCode = 1;
        }

        // if shutdown handler
        if (!$manual) {
            /**
             * Warning: this part will always be executed,
             * independent of the outcome of the script.
             */
            ErrorHandler::restore();
        }
        exit($statusCode);
    }

    final protected function execute(): ResponseInterface
    {
        $classType = PhpHelper::isCli()
            ? 'Command'
            : 'Controller';
        $target = $this->request()->getTarget();
        // \WebServCo\Framework\Objects\Route
        $route = $this->router()->getRoute(
            $target,
            $this->router()->setting('routes', []),
            $this->request()->getArgs(),
        );

        $className = sprintf("\\%s\\Domain\\%s\\%s", $this->projectNamespace, $route->class, $classType);
        if (!class_exists($className)) {
            if ($classType !== 'Controller') {
                throw new NonApplicationException(
                    sprintf('No matching %s found. Target: "%s"', $classType, $target),
                );
            }
            // Class type is "Controller", so check for 404 route
            // throws \WebServCo\Framework\Exceptions\NotFoundException
            $route = $this->router()->getFourOhfourRoute();
            $className = sprintf("\\%s\\Domain\\%s\\%s", $this->projectNamespace, $route->class, $classType);
        }

        $object = new $className();
        $parent = get_parent_class($object);
        /**
         * PHP 8 compatibility.
         *
         * https://www.php.net/manual/en/migration80.incompatible.php
         * "The ability to call non-static methods statically has been removed.
         * Thus is_callable() will fail when checking for a non-static method with a classname
         * (must check with an object instance). "
         * old line:
         * if (\method_exists((string) $parent, $route->method) || !\is_callable([$className, $route->method])) {
         */
        if (method_exists((string) $parent, $route->method) || !method_exists($className, $route->method)) {
            throw new NonApplicationException(sprintf('No valid matching Action found. Target: "%s".', $target));
        }
        $callable = [$object, $route->method];
        if (!is_callable($callable)) {
            throw new NonApplicationException(sprintf('Method is not callable. Target: "%s"', $target));
        }

        return call_user_func_array($callable, $route->arguments);
    }

    /**
     * Handle Errors.
     */
    final protected function handleErrors(?Throwable $exception = null): bool
    {
        $throwable = ErrorObjectHelper::get($exception);
        if ($throwable instanceof Throwable) {
            return $this->halt($throwable);
        }

        return false;
    }

    final protected function halt(Throwable $throwable): bool
    {
        return PhpHelper::isCli()
            ? $this->haltCli($throwable)
            : $this->haltHttp($throwable);
    }

    abstract protected function getCliOutput(Throwable $throwable): string;

    abstract protected function getHttpOutput(Throwable $throwable): string;

    abstract protected function getResponse(): ResponseInterface;

    public function __construct(string $publicPath, ?string $projectPath, ?string $projectNamespace)
    {
        // If no custom namespace is set, use "Project".
        $this->projectNamespace = $projectNamespace ?? 'Project';

        // Make sure path ends with a slash.
        $publicPath = rtrim($publicPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_readable($publicPath . 'index.php')) {
            throw new NonApplicationException('Public web path is not readable.');
        }

        // If no custom project path is set, use parent directory of public.
        $projectPath ??= (string) realpath(sprintf('%s..', $publicPath));

        // Make sure path ends with a slash.
        $this->projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        // Add environment settings.

        Setting::set('APP_PATH_WEB', $publicPath);
        Setting::set('APP_PATH_PROJECT', $this->projectPath);
        Setting::set('APP_PATH_LOG', sprintf('%svar/log/', $this->projectPath));
    }

    protected function haltCli(Throwable $throwable): bool
    {
        $output = $this->getCliOutput($throwable);
        $response = new Response($output, 1);
        $response->send();

        return true;
    }

    protected function haltHttp(Throwable $throwable): bool
    {
        $output = $this->getHttpOutput($throwable);

        $code = $throwable->getCode();
        switch ($code) {
            // non-HTTP
            case 0:
            // non-HTTP
            case -1:
                $statusCode = 500;

                break;
            default:
                $statusCodes = StatusCode::getSupported();
                $statusCode = array_key_exists($code, $statusCodes)
                    //  Use Throwable status code as it is a valid HTTP code
                    ? $code
                    : 500;

                break;
        }

        $response = new HttpResponse(
            $output,
            $statusCode,
            ['Content-Type' => ['text/html']],
        );
        $response->send();

        return true;
    }

    protected function loadEnvironmentSettings(): bool
    {
        return Settings::load($this->projectPath);
    }
}
