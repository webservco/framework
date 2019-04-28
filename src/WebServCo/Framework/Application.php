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
        } catch (\Exception $e) { // php5
            $this->shutdown($e, true);
        }
    }

    /**
     * Runs the application.
     */
    final public function run()
    {
        try {
            $response = $this->execute();
            $statusCode = 0;
            if ($response instanceof
                \WebServCo\Framework\Interfaces\ResponseInterface) {
                $statusCode = $response->send();
            }
            $this->shutdown(null, true, Framework::isCli() ? $statusCode : 0);
        } catch (\Throwable $e) { // php7
            $this->shutdown($e, true);
        } catch (\Exception $e) { // php5
            $this->shutdown($e, true);
        }
    }

    final protected function execute()
    {
        $classType = Framework::isCli() ? 'Command' : 'Controller';
        $route = $this->router()->getRoute(
            $this->request()->getTarget(),
            $this->router()->setting('routes'),
            $this->request()->getArgs()
        );

        $class = isset($route[0]) ? $route[0] : null;
        $method = isset($route[1]) ? $route[1] : null;
        $args = isset($route[2]) ? $route[2] : [];

        if (empty($class) || empty($method)) {
            throw new ApplicationException("Invalid route");
        }

        $className = sprintf("\\%s\\Domain\\%s\\%s", $this->projectNamespace, $class, $classType);
        if (!class_exists($className)) {
            /* enable in V10 *
            throw new NotFoundException(
                sprintf('No matching %s found', $classType)
            );
            /* enable in V10 */

            /* remove in V10 */
            // check for v9 class name
            $className = sprintf("\\%s\\Domain\\%s\\%s%s", $this->projectNamespace, $class, $class, $classType);
            if (!class_exists($className)) {
                throw new NotFoundException(
                    sprintf('No matching %s found', $classType)
                );
            }
            /* remove in V10 */
        }

        $object = new $className;
        $parent = get_parent_class($object);
        if (method_exists((string) $parent, $method) ||
            !is_callable([$className, $method])) {
            throw new NotFoundException('No matching Action found');
        }
        $callable = [$object, $method];
        if (!is_callable($callable)) {
            throw new ApplicationException('Method not found');
        }
        return call_user_func_array($callable, $args);
    }

    /**
     * Finishes the execution of the Application.
     *
     * This method is also registered as a shutdown handler.
     */
    final public function shutdown($exception = null, $manual = false, $statusCode = 0)
    {
        $hasError = $this->handleErrors($exception);
        if ($hasError) {
            $statusCode = 1;
        }

        if (!$manual) { //if shutdown handler
            /**
             * Warning: this part will always be executed,
             * independent of the outcome of the script.
             */
            ErrorHandler::restore();
        }
        exit($statusCode);
    }
}
