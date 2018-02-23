<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Settings as S;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Environment as Env;
use WebServCo\Framework\ErrorHandler as Err;
use WebServCo\Framework\Exceptions\ApplicationException;
use WebServCo\Framework\Exceptions\NotFoundException;

class Application extends \WebServCo\Framework\AbstractApplication
{
    protected $projectPath;
    
    public function __construct($publicPath, $projectPath)
    {
        $publicPath = rtrim($publicPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        if (!is_readable($publicPath . 'index.php') || !is_readable($this->projectPath . '.env')) {
            throw new ApplicationException(
                'Invalid paths specified when initializing Application.'
            );
        }
        
        $this->config()->set(sprintf('app%1$spath%1$sweb', S::DIVIDER), $publicPath);
        $this->config()->set(sprintf('app%1$spath%1$sproject', S::DIVIDER), $this->projectPath);
    }
    
    /**
     * Sets the env value from the project .env file.
     */
    final public function setEnvironmentValue()
    {
        /**
         * Env file existence is verified in the controller.
         */
        $this->config()->setEnv(trim(file_get_contents($this->projectPath . '.env')));
        
        return true;
    }
    
    /**
     * Starts the execution of the application.
     */
    final public function start()
    {
        try {
            Err::set();
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
                $statusCode = $response->send($this->request());
                $this->shutdown(
                    null,
                    true,
                    Fw::isCLI() ? $statusCode : 0
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
        $classType = Fw::isCLI() ? 'Command' : 'Controller';
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
        if (method_exists($parent, $method) ||
            !is_callable([$className, $method])) {
            throw new NotFoundException('No matching Action found');
        }
        return call_user_func_array([$object, $method], $args);
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
            Err::restore();
        }
        exit($statusCode);
    }
}
