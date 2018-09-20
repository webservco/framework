<?php
namespace WebServCo\Framework;

use WebServCo\Framework\ErrorHandler;
use WebServCo\Framework\Framework;
use WebServCo\Framework\Settings;
use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Exceptions\NotFoundException;

class Application extends \WebServCo\Framework\AbstractApplication
{
    protected $projectPath;

    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;

    public function __construct($publicPath, $projectPath)
    {
        $publicPath = rtrim($publicPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

        if (!is_readable($publicPath . 'index.php') || !is_readable($this->projectPath . '.env')) {
            throw new ApplicationException(
                'Invalid paths specified when initializing Application.'
            );
        }

        $this->config()->set(sprintf('app%1$spath%1$sweb', Settings::DIVIDER), $publicPath);
        $this->config()->set(sprintf('app%1$spath%1$sproject', Settings::DIVIDER), $this->projectPath);
    }

    /**
     * Sets the env value from the project .env file.
     */
    final public function setEnvironmentValue()
    {
        /**
         * Env file existence is verified in the controller.
         */
        $this->config()->setEnv(trim((string)file_get_contents($this->projectPath . '.env')));

        return true;
    }

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
            if ($response instanceof
                \WebServCo\Framework\Interfaces\ResponseInterface) {
                $statusCode = $response->send();
                $this->shutdown(
                    null,
                    true,
                    Framework::isCLI() ? $statusCode : 0
                );
            }
        } catch (\Throwable $e) { // php7
            $this->shutdown($e, true);
        } catch (\Exception $e) { // php5
            $this->shutdown($e, true);
        }
    }

    final protected function execute()
    {
        $classType = Framework::isCLI() ? 'Command' : 'Controller';
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
        $className = "\\Project\\Domain\\{$class}\\{$class}{$classType}";
        if (!class_exists($className)) {
            throw new NotFoundException(
                sprintf('No matching %s found', $classType)
            );
        }
        $object = new $className;
        $parent = get_parent_class($object);
        if (method_exists((string) $parent, $method) ||
            !is_callable([$className, $method])) {
            throw new NotFoundException('No matching Action found');
        }
        $callable = [$object, $method];
        if (is_callable($callable)) {
            return call_user_func_array($callable, $args);
        }
        throw new ApplicationException('Method not found');
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
