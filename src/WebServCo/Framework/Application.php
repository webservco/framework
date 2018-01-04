<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

class Application
{
    public function __construct($pathPublic, $pathProject)
    {
        $pathPublic = rtrim($pathPublic, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $pathProject = rtrim($pathProject, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        if (!is_readable("{$pathPublic}index.php") || !is_readable("{$pathProject}.env")) {
            throw new \ErrorException(
                'Invalid paths specified when initializing Application.'
            );
        }
        Fw::config()->set('app.path.web', $pathPublic);
        Fw::config()->set('app.path.project', $pathProject);
    }
    
    /**
     * Sets the env value from the project .env file.
     */
    public function setEnvironmentValue()
    {
        /**
         * Project path is set in the constructor.
         */
        $pathProject = Fw::config()->get('app.path.project');
        /**
         * Env file existence is verified in the controller.
         */
        Fw::config()->setEnv(trim(file_get_contents("{$pathProject}.env")));
        
        return true;
    }
    
    /**
     * Starts the execution of the application.
     */
    public function start()
    {
        \WebServCo\Framework\ErrorHandler::set();
        register_shutdown_function([$this, 'shutdown']);
        
        try {
            $this->setEnvironmentValue();
            Fw::date()->setTimezone();
            return true;
        } catch (\Errors $e) { //php 7
            return $this->shutdown($e, true);
        } catch (\Exception $e) {
            return $this->shutdown($e, true);
        }
    }
    
    /**
     * Finishes the execution of the Application.
     *
     * This method is also registered as a shutdown handler.
     */
    public function shutdown($exception = null, $manual = false)
    {
        $this->handleErrors($exception);
        
        if (!$manual) { //if shutdown handler
            /**
             * Warning: this part will always be executed,
             * independent of the outcome of the script.
             */
            
            \WebServCo\Framework\ErrorHandler::restore();
        }
        exit;
    }
    
    /**
     * Handle Errors.
     *
     * @param mixed $exception and Error or Exception object.
     */
    public function handleErrors($exception = null)
    {
        if (is_object($exception)) {
            $errorInfo = [
                'code' => $exception->getCode(),
                'severity' => $exception instanceof ErrorException ? $exception->getSeverity() : null,
                'message' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ];
        } else {
            $errorInfo = error_get_last();
        }
        
        if (!empty($errorInfo)) {
            if (!Fw::isCLI()) {
                header('HTTP/1.1 500 Internal Server Error');
            }
            echo 'The Application made a boo boo.' . PHP_EOL;
            if (\WebServCo\Framework\Environment::ENV_DEV === Fw::config()->getEnv()) {
                echo $errorInfo['message'] . PHP_EOL;
                if (isset($errorInfo['code'])) {
                    echo $errorInfo['file'] . ':' . $errorInfo['line'] . PHP_EOL;
                }
            }
            return true;
        }
        return false;
    }
    
    /**
     * Runs the application.
     */
    public function run()
    {
    }
}
