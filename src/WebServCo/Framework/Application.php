<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

class Application
{
    public function __construct($pathPublic, $pathProject)
    {
        
        /*
        $this->setEnvironment($pathProject);
        
        Fw::config()->set('app.path.public', $pathPublic);
        Fw::config()->set('app.path.project', $pathProject);
        */
        //set local configuration
    }
    
    private function setEnvironment($pathProject)
    {
        $env = Environment::ENV_DEV;
        if (is_readable("{$pathProject}.env")) {
            $env = trim(include "{$pathProject}.env");
        }
        if (in_array($env, Environment::getOptions())) {
            //XXX
        }
    }
    
    public function boot()
    {
        if (Fw::isCLI()) {
            return $this->bootCLI();
        } else {
            return $this->bootHTTP();
        }
    }
    
    protected function bootCLI()
    {
    }
    
    protected function bootHTTP()
    {
    }
}
