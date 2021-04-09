<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Exceptions\NotFoundException;
use WebServCo\Framework\Helpers\PhpHelper;
use WebServCo\Framework\Interfaces\ResponseInterface;

class Application extends \WebServCo\Framework\AbstractApplication
{
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;

    /**
     * Starts the execution of the application.
     */
    final public function start(): void
    {
        try {
            ErrorHandler::set();

            \register_shutdown_function([$this, 'shutdown']);

            $this->setEnvironmentValue();

            $this->loadEnvironmentConfiguration();
        } catch (\Throwable $e) {
            $this->shutdown($e, true);
        }
    }

    /**
     * Runs the application.
     */
    public function run(): void
    {
        try {
            $response = $this->execute();
            $statusCode = 0;
            if ($response instanceof ResponseInterface) {
                $statusCode = $response->send();
            }
            $this->shutdown(null, true, PhpHelper::isCli() ? $statusCode : 0);
        } catch (\Throwable $e) {
            $this->shutdown($e, true);
        }
    }

    /**
     * Finishes the execution of the Application.
     *
     * This method is also registered as a shutdown handler.
     */
    final public function shutdown(?\Throwable $exception = null, bool $manual = false, int $statusCode = 0): void
    {
        $hasError = $this->handleErrors($exception);
        if ($hasError) {
            $statusCode = 1;
        }

        if (!$manual) { // if shutdown handler
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
            $this->router()->setting('routes'),
            $this->request()->getArgs(),
        );

        $className = \sprintf("\\%s\\Domain\\%s\\%s", $this->projectNamespace, $route->class, $classType);
        if (!\class_exists($className)) {
            if ('Controller' !== $classType) {
                throw new NotFoundException(
                    \sprintf('No matching %s found. Target: "%s"', $classType, $target),
                );
            }
            // Class type is "Controller", so check for 404 route
            // throws \WebServCo\Framework\Exceptions\NotFoundException
            $route = $this->router()->getFourOhfourRoute();
            $className = \sprintf("\\%s\\Domain\\%s\\%s", $this->projectNamespace, $route->class, $classType);
        }

        $object = new $className();
        $parent = \get_parent_class($object);
        if (\method_exists((string) $parent, $route->method) || !\is_callable([$className, $route->method])) {
            throw new NotFoundException(\sprintf('No matching Action found. Target: "%s".', $target));
        }
        $callable = [$object, $route->method];
        if (!\is_callable($callable)) {
            throw new ApplicationException(\sprintf('Method not found. Target: "%s"', $target));
        }

        return \call_user_func_array($callable, $route->arguments);
    }
}
