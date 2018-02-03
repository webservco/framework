<?php
namespace WebServCo\Framework;

abstract class AbstractController
{
    use \WebServCo\Framework\Traits\OutputTrait;
    
    public function __construct($outputLoader)
    {
        $this->outputLoader = $outputLoader;
    }
}
