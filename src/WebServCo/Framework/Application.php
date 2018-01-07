<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Settings as S;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Environment as Env;
use WebServCo\Framework\ErrorHandler as Err;

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
        
        Fw::config()->set(sprintf('app%1$spath%1$sweb', S::DIVIDER), $pathPublic);
        Fw::config()->set(sprintf('app%1$spath%1$sproject', S::DIVIDER), $pathProject);
    }
    
    /**
     * Sets the env value from the project .env file.
     */
    final public function setEnvironmentValue()
    {
        /**
         * Project path is set in the constructor.
         */
        $pathProject = Fw::config()->get(sprintf('app%1$spath%1$sproject', S::DIVIDER));
        /**
         * Env file existence is verified in the controller.
         */
        Fw::config()->setEnv(trim(file_get_contents("{$pathProject}.env")));
        
        return true;
    }
    
    /**
     * Starts the execution of the application.
     */
    final public function start()
    {
        Err::set();
        register_shutdown_function([$this, 'shutdown']);
        
        try {
            $this->setEnvironmentValue();
            Fw::date()->setTimezone();
            /**
             * @todo i18n init
             */
            /**
             * @todo log (psr3)
             */
            /**
             * @todo sanitize request
             */
            /**
             * @todo routing (if not cli) XXX
             */
            /**
             * @todo session (if not cli)
             */
            /**
             * @todo session i18n functionality (if not cli)
             */
            /**
             * @todo user init (if not cli)
             */
            /**
             * @todo i18n
             */
            
            return true;
        } catch (\Throwable $e) { //php > 7
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
    final public function shutdown($exception = null, $manual = false)
    {
        $this->handleErrors($exception);
        
        if (!$manual) { //if shutdown handler
            /**
             * Warning: this part will always be executed,
             * independent of the outcome of the script.
             */
            
            Err::restore();
        }
        exit;
    }
    
    /**
     * Runs the application.
     */
    final public function run()
    {
        if (Fw::isCLI()) {
            return $this->runCli();
        } else {
            return $this->runHttp();
        }
    }
    
    public function runHttp()
    {
        try {
            list($class, $method, $args) =
            Fw::router()->getRoute(
                Fw::request()->target,
                Fw::router()->setting('routes')
            );
            $className = "\\Project\\Domain\\{$class}\\Http\\{$class}Controller";
            if (!class_exists($className)) {
                throw new \ErrorException('No matching controller found', 404);
            }
            $object = new $className;
            $parent = get_parent_class($object);
            if (method_exists($parent, $method) ||
                !is_callable([$className, $method])) {
                throw new \ErrorException('No matching action found', 404);
            }
            return call_user_func_array([$object, $method], $args);
        } catch (\Throwable $e) { //php > 7
            return $this->shutdown($e, true);
        } catch (\Exception $e) {
            return $this->shutdown($e, true);
        }
        return true;
    }
    
    public function runCli()
    {
        try {
            throw new \ErrorException(
                'CLI support not implemented yet,'.
                ' please open this resource in a web browser.'
            );
        } catch (\Throwable $e) { //php > 7
            return $this->shutdown($e, true);
        } catch (\Exception $e) {
            return $this->shutdown($e, true);
        }
        return true;
    }
    
    /**
     * Handle Errors.
     *
     * @param mixed $exception An Error or Exception object.
     */
    final private function handleErrors($exception = null)
    {
        $errorInfo = [
            'code' => 0,
            'severity' => null,
            'message' => null,
            'file' => null,
            'line' => null,
            'trace' => null,
        ];
        if (is_object($exception)) {
            $errorInfo['code'] = $exception->getCode();
            if ($exception instanceof ErrorException) {
                $errorInfo['severity'] = $exception->getSeverity();
            }
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
    
    final private function halt($errorInfo = [])
    {
        if (Fw::isCLI()) {
            return $this->haltCli($errorInfo);
        } else {
            return $this->haltHttp($errorInfo);
        }
    }
    
    public function haltHttp($errorInfo = [])
    {
        switch ($errorInfo['code']) {
            case 404:
                $statusCode = 404;
                $title = 'Resource not found';
                break;
            case 500:
            default:
                $statusCode = 500;
                $title = 'The App made a boo boo';
                break;
        }
        Fw::response()->setStatusHeader($statusCode);
        
        echo '<!doctype html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Sorry</title>
            <style>
            * {
                background: #436f4d; color: #fff;
            }
            .o {
                display: table; position: absolute; height: 100%; width: 100%;
            }

            .m {
                display: table-cell; vertical-align: middle;
            }

            .i {
                margin-left: auto; margin-right: auto; width: 680px; text-align: center;
            }
            small {
                font-size: 0.7em;
            }
            </style>
        </head>
        <body>
            <div class="o">
                <div class="m">
                    <div class="i">';
        echo "<h1>{$title}</h1>";
        if (Env::ENV_DEV === Fw::config()->getEnv()) {
            echo "<p>$errorInfo[message]</p>";
            echo "<p><small>$errorInfo[file]:$errorInfo[line]</small></p>";
        }
        echo '      </div>
                </div>
            </div>
        </body>
        </html>';
        return true;
    }
    
    public function haltCli($errorInfo = [])
    {
        echo 'The App made a boo boo' . PHP_EOL;
        if (Env::ENV_DEV === Fw::config()->getEnv()) {
            echo $errorInfo['message'] . PHP_EOL;
            echo "$errorInfo[file]:$errorInfo[line]" . PHP_EOL;
        }
        return true;
    }
}
