<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Framework as Fw;

abstract class AbstractRepository
{
    use \WebServCo\Framework\Traits\OutputTrait;
    
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
    
    final protected function i18n()
    {
        return Fw::getLibrary('I18n');
    }
    
    final protected function request()
    {
        return Fw::getLibrary('Request');
    }
    
    final protected function session()
    {
        return Fw::getLibrary('Session');
    }
}
