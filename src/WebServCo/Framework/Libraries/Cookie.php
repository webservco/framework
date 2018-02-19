<?php
namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\RequestUtils as Utils;

final class Cookie extends \WebServCo\Framework\AbstractLibrary
{
    public function __construct($config)
    {
        parent::__construct($config);
    }
    
    public function set(
        $name,
        $value = '',
        $expire = 0,
        $path = '',
        $domain = '',
        $secure = false,
        $httponly = false
    ) {
        return setcookie(
            $name,
            Utils::sanitizeString($value),
            $expire,
            $path,
            $domain,
            $secure,
            $httponly
        );
    }
    
    public function get($name, $defaultValue = false)
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $defaultValue;
    }
    
    public function remove($name)
    {
        if (!isset($_COOKIE[$name])) {
            return false;
        }
        
        unset($_COOKIE[$name]);
        $this->set($name, false, -1);
        return true;
    }
}
