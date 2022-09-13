<?php

namespace WebServCo\Framework;

use WebServCo\Framework\ErrorHandler;
use WebServCo\Framework\Framework;
use WebServCo\Framework\Settings;
use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Exceptions\NotFoundException;

class Application extends \WebServCo\Framework\AbstractApplication
{
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;

    /**
     * Starts the execution of the application.
     */
    final public function start()
    {
        try {
            ErrorHandler::set();

            register_shutdown_function([$this, 'shutdown']);

            $this->setEnvironmentValue();

            /**
             * With no argument, timezone will be set from the configuration.
             */
            $this->date()->setTimezone();
            /**
             * @todo i18n, log, session (if not cli), users (if not cli)
             */

            return true;
        } catch (\Throwable $e) { // php7
            $this->shutdown($e, true);
        }
    }

    /**
     * Runs the application.
     */
    public function run()
    {
        try {
            $response = $this->execute();
            $statusCode = 0;
            if (
                $response instanceof
                \WebServCo\Framework\Interfaces\ResponseInterface
            ) {
                $statusCode = $response->send();
            }
            $this->shutdown(null, true, Framework::isCli() ? $statusCode : 0);
        } catch (\Throwable $e) { // php7
            $this->shutdown($e, true);
        }
    }

    final protected function execute()
    {
        $classType = Framework::isCli() ? 'Command' : 'Controller';
        $target = $this->request()->getTarget();
        $route = $this->router()->getRoute(
            $target,
            $this->router()->setting('routes'),
            $this->request()->getArgs()
        );

        $class = isset($route[0]) ? $route[0] : null;
        $method = isset($route[1]) ? $route[1] : null;
        $args = isset($route[2]) ? $route[2] : [];

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
            /* enable in V10 *
            throw new NotFoundException(
                sprintf('No matching %s found. Target: "%s"', $classType, $target)
            );
            /* enable in V10 */

            /* remove in V10 */
            // check for v9 class name
            $className = sprintf("\\%s\\Domain\\%s\\%s%s", $this->projectNamespace, $class, $class, $classType);
            if (!class_exists($className)) {
                throw new NotFoundException(
                    sprintf('No matching %s found. Target: "%s".', $classType, $target)
                );
            }
            /* remove in V10 */
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
         * if (method_exists((string) $parent, $method) || !is_callable([$className, $method])) {
         */
        if (\method_exists((string) $parent, $method) || !\method_exists($className, $method)) {
            throw new NotFoundException(sprintf('No valid matching Action found. Target: "%s".', $target));
        }
        $callable = [$object, $method];
        if (!is_callable($callable)) {
            throw new ApplicationException(sprintf('Method is not callable. Target: "%s"', $target));
        }
        return call_user_func_array($callable, $args);
    }

    /**
     * Finishes the execution of the Application.
     *
     * This method is also registered as a shutdown handler.
     */
    final public function shutdown($exception = null, $manual = false, $statusCode = 0): void
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
