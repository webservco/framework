<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

class Application
{
    public function __construct($pathPublic, $pathProject)
    {
        if (!is_readable($pathPublic) || !is_readable("{$pathProject}.env")) {
            throw new \ErrorException(
                'Invalid paths specified'
            );
        }
        Fw::config()->set('app.path.web', $pathPublic);
        Fw::config()->set('app.path.project', $pathProject);
        
        \WebServCo\Framework\ErrorHandler::set();
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
        $this->setEnvironmentValue();
        
        return true;
    }
    
    /**
     * Finishes the execution of the Application.
     */
    public function stop()
    {
        \WebServCo\Framework\ErrorHandler::restore();
        
        return true;
    }
    
    /**
     * Runs the applciation.
     */
    public function run()
    {
    }
}
