<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

final class Cookie extends \WebServCo\Framework\AbstractLibrary
{
    public function get(string $name, ?string $defaultValue): string
    {
        return isset($_COOKIE[$name]) ? $_COOKIE[$name] : $defaultValue;
    }

    public function remove(string $name): bool
    {
        if (!isset($_COOKIE[$name])) {
            return false;
        }

        unset($_COOKIE[$name]);
        $this->set($name, '', -1);
        return true;
    }

    public function set(
        string $name,
        string $value = '',
        int $expires = 0,
        string $path = '',
        string $domain = '',
        bool $secure = true,
        bool $httponly = false,
        string $samesite = 'Lax'
    ): bool {
        if (!in_array($samesite, ['Lax'])) {
            throw new \InvalidArgumentException('Invalid argument: samesite.');
        }
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
