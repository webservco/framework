<?php
namespace WebServCo\Framework\Libraries;

final class Cookie extends \WebServCo\Framework\AbstractLibrary
{
    public function set(
        $name,
        $value = '',
        $expire = 0,
        $path = '',
        $domain = '',
        $secure = true,
        $httponly = false
    ) {
        return setcookie(
            $name,
            \WebServCo\Framework\RequestUtils::sanitizeString($value),
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
