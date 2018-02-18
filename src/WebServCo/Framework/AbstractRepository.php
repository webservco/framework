<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

abstract class AbstractRepository
{
    use \WebServCo\Framework\Traits\OutputTrait;
    use \WebServCo\Framework\Traits\ExposeLibrariesTrait;
    
    public function __construct($outputLoader)
    {
        $this->outputLoader = $outputLoader;
    }
    
    final protected function pdoDb()
    {
        return Fw::getLibrary('PdoDatabase');
    }
    
    final protected function mysqliDb()
    {
        return Fw::getLibrary('MysqliDatabase');
    }
}
