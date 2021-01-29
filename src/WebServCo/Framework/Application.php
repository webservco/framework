<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\ErrorHandler;
use WebServCo\Framework\Framework;
use WebServCo\Framework\Settings;
use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Exceptions\NotFoundException;
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

            register_shutdown_function([$this, 'shutdown']);

            $this->setEnvironmentValue();
        } catch (\Throwable $e) { // php7
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
            $this->shutdown(null, true, Framework::isCli() ? $statusCode : 0);
        } catch (\Throwable $e) { // php7
            $this->shutdown($e, true);
        }
    }

    final protected function execute(): ResponseInterface
    {
        $classType = Framework::isCli() ? 'Command' : 'Controller';
        $target = $this->request()->getTarget();
        $route = $this->router()->getRoute(
            $target,
            $this->router()->setting('routes'),
            $this->request()->getArgs()
        );

        $class = isset($route[0]) ? strval($route[0]): null;
        $method = isset($route[1]) ? strval($route[1]): null;
        $args = isset($route[2]) ? $route[2] : [];
        if (!is_array($args)) {
            $args = [];
        }

        if (empty($class) || empty($method)) {
            throw new ApplicationException(
                sprintf(
                    'Invalid route. Target: "%s".',
                    $target
                )
            );
        }

        $className = sprintf("\\%s\\Domain\\%s\\%s", $this->projectNamespace, $class, $classType);
        if (!class_exists($className)) {
            throw new NotFoundException(
                sprintf('No matching %s found. Target: "%s"', $classType, $target)
            );
        }

        $object = new $className;
        $parent = get_parent_class($object);
        if (method_exists((string) $parent, $method) ||
            !is_callable([$className, $method])) {
            throw new NotFoundException(sprintf('No matching Action found. Target: "%s".', $target));
        }
        $callable = [$object, $method];
        if (!is_callable($callable)) {
            throw new ApplicationException(sprintf('Method not found. Target: "%s"', $target));
        }

        return call_user_func_array($callable, $args);
    }

    /**
     * Finishes the execution of the Application.
     *
     * This method is also registered as a shutdown handler.
     */
    final public function shutdown(\Throwable $exception = null, bool $manual = false, int $statusCode = 0): void
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
}
