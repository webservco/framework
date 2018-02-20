<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Framework as Fw;

trait ExposeLibrariesTrait
{
    final protected function config()
    {
        return Fw::getLibrary('Config');
    }
    
    final protected function cookie()
    {
        return Fw::getLibrary('Cookie');
    }
    
    final protected function i18n()
    {
        return Fw::getLibrary('I18n');
    }
    
    final protected function mysqliDb()
    {
        return Fw::getLibrary('MysqliDatabase');
    }
    
    final protected function pdoDb()
    {
        return Fw::getLibrary('PdoDatabase');
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
