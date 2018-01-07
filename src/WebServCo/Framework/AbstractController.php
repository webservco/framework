<?php
namespace WebServCo\Framework;

abstract class AbstractController
{
    protected $outputLoader;
    
    public function __construct($outputLoader)
    {
        $this->outputLoader = $outputLoader;
    }
    
    final protected function output()
    {
        return $this->outputLoader;
    }
}
