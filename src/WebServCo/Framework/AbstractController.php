<?php
namespace WebServCo\Framework;

abstract class AbstractController
{
    use \WebServCo\Framework\Traits\OutputTrait;
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;
    use \WebServCo\Framework\Traits\ResponseUrlTrait;
    
    public function __construct($outputLoader)
    {
        $this->outputLoader = $outputLoader;
    }
}
