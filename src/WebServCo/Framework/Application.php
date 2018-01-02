<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

class Application
{
    public function __construct($dirPublic, $dirProject)
    {
        $pathPublic = "{$dirPublic}/";
        $pathProject = realpath($dirProject . '/..') . '/';
        
        Fw::config()->set('app.path.public', $pathPublic);
        Fw::config()->set('app.path.project', $pathProject);
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
