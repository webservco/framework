<?php
namespace WebServCo\Framework\Traits;

trait OutputTrait
{
    protected $outputLoader;
    
    final protected function output()
    {
        return $this->outputLoader;
    }
    
    final protected function echo($string, $eol = true)
    {
        return $this->output()->write($string, $eol);
    }
}
