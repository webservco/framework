<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Settings as S;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Environment as Env;
use WebServCo\Framework\ErrorHandler as Err;

class Application
{
    public function __construct($publicPath, $projectPath)
    {
        $publicPath = rtrim($publicPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $projectPath = rtrim($projectPath, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        
        if (!is_readable("{$publicPath}index.php") || !is_readable("{$projectPath}.env")) {
            throw new \ErrorException(
                'Invalid paths specified when initializing Application.'
            );
        }
        
        $this->config()->set(sprintf('app%1$spath%1$sweb', S::DIVIDER), $publicPath);
        $this->config()->set(sprintf('app%1$spath%1$sproject', S::DIVIDER), $projectPath);
    }
    
    final public function config()
    {
        return Fw::getLibrary('Config');
    }
    
    final protected function date()
    {
        return Fw::getLibrary('Date');
    }
    
    final protected function router()
    {
        return Fw::getLibrary('Router');
    }
    
    final protected function request()
    {
        return Fw::getLibrary('Request');
    }
    
    final protected function response()
    {
        return Fw::getLibrary('Response');
    }
    
    /**
     * Sets the env value from the project .env file.
     */
    final public function setEnvironmentValue()
    {
        /**
         * Project path is set in the constructor.
         */
        $pathProject = $this->config()->get(sprintf('app%1$spath%1$sproject', S::DIVIDER));
        /**
         * Env file existence is verified in the controller.
         */
        $this->config()->setEnv(trim(file_get_contents("{$pathProject}.env")));
        
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
            
            /**
             * With no argument, timezone will be set from the configuration.
             */
            $this->date()->setTimezone();
            /**
             * @todo i18n, log, session (if not cli), users (if not cli)
             */
            
            return true;
        } catch (\Throwable $e) { // php7
            return $this->shutdown($e, true);
        } catch (\Exception $e) { // php5
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
        try {
            $classType = Fw::isCLI() ? 'Command' : 'Controller';
            list($class, $method, $args) =
            $this->router()->getRoute(
                $this->request()->target,
                $this->router()->setting('routes'),
                $this->request()->args
            );
            $className = "\\Project\\Domain\\{$class}\\{$class}{$classType}";
            if (!class_exists($className)) {
                throw new \ErrorException("No matching {$classType} found", 404);
            }
            $object = new $className;
            $parent = get_parent_class($object);
            if (method_exists($parent, $method) ||
                !is_callable([$className, $method])) {
                throw new \ErrorException('No matching action found', 404);
            }
            return call_user_func_array([$object, $method], $args);
        } catch (\Throwable $e) { // php7
            return $this->shutdown($e, true);
        } catch (\Exception $e) { // php5
            return $this->shutdown($e, true);
        }
    }
    
    /**
     * Handle Errors.
     *
     * @param mixed $exception An \Error or \Exception object.
     */
    final private function handleErrors($exception = null)
    {
        $errorInfo = [
            'code' => 0,
            'message' => null,
            'file' => null,
            'line' => null,
            'trace' => null,
        ];
        if ($exception instanceof \Throwable ||
            $exception instanceof \Exception
        ) {
            $errorInfo['code'] = $exception->getCode();
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
    
    protected function haltHttp($errorInfo = [])
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
        $this->response()->setStatusHeader($statusCode);
        
        echo '<!doctype html>
        <html>
        <head>
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Oups</title>
            <style>
            * {
                background: #f2dede; color: #a94442;
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
        if (Env::ENV_DEV === $this->config()->getEnv()) {
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
    
    protected function haltCli($errorInfo = [])
    {
        echo 'The App made a boo boo' . PHP_EOL;
        if (Env::ENV_DEV === $this->config()->getEnv()) {
            echo $errorInfo['message'] . PHP_EOL;
            echo "$errorInfo[file]:$errorInfo[line]" . PHP_EOL;
        }
        return true;
    }
}
