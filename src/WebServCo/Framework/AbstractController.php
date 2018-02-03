<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

abstract class AbstractController
{
    use \WebServCo\Framework\Traits\OutputTrait;
    
    public function __construct($outputLoader)
    {
        $this->outputLoader = $outputLoader;
    }
    
    final protected function config()
    {
        return Fw::getLibrary('Config');
    }
    
    final protected function request()
    {
        return Fw::getLibrary('Request');
    }
}
