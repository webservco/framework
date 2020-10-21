<?php
namespace WebServCo\Framework\Libraries;

final class Cookie extends \WebServCo\Framework\AbstractLibrary
{
    public function set(
        $name,
        $value = '',
        $expires = 0,
        $path = '',
        $domain = '',
        $secure = true,
        $httponly = false,
        $samesite = 'Lax'
    ) {
        if (PHP_VERSION_ID < 70300) {
            return setcookie(
                $name,
                \WebServCo\Framework\RequestUtils::sanitizeString($value),
                $expires,
                sprintf('%s; SameSite=%s', $path, $samesite),
                $domain,
                $secure,
                $httponly
            );
        } else {
            return setcookie(
                $name,
                \WebServCo\Framework\RequestUtils::sanitizeString($value),
                [
                    'expires' => $expires,
                    'path' => $path,
                    'domain' => $domain,
                    'secure' => $secure,
                    'httponly' => $httponly,
                    'samesite' => $samesite,
                ]
            );
        }
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
