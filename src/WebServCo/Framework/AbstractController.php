<?php
namespace WebServCo\Framework;

abstract class AbstractController
{
    use \WebServCo\Framework\Traits\OutputTrait;
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;
    
    public function __construct($outputLoader)
    {
        $this->outputLoader = $outputLoader;
    }
}
