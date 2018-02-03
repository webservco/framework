<?php
namespace WebServCo\Framework;

abstract class AbstractRepository
{
    use \WebServCo\Framework\Traits\OutputTrait;
    
    public function __construct($outputLoader)
    {
        $this->outputLoader = $outputLoader;
    }
}
