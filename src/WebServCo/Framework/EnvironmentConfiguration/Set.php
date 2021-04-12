<?php

declare(strict_types=1);

namespace WebServCo\Framework\EnvironmentConfiguration;

use WebServCo\Framework\Exceptions\ConfigurationException;

final class Set
{
    /**
    * @param mixed $value
    */
    public static function do(string $key, $value): bool
    {
        $key = self::getValidatedKey($key);
        switch ($key) {
            case 'APP_ENVIRONMENT':
                \WebServCo\Framework\Environment::validate($value);
                break;
            default:
                break;
        }
        $_SERVER[$key] = $value;
        return true;
    }

    public static function getValidatedKey(string $key): string
    {
        $key = \strtoupper($key);
        if (!\WebServCo\Framework\Utils\Strings::startsWith($key, 'APP_', false)) {
            throw new ConfigurationException(\sprintf('Invalid key name: "%s".', $key));
        }
        return $key;
    }
}
