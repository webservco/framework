<?php

declare(strict_types=1);

namespace WebServCo\Framework\EnvironmentConfiguration;

use WebServCo\Framework\Exceptions\ConfigurationException;

final class Config
{
    /**
    * Get an environment configuration value.
    *
    * Key existence is validated.
    *
    * @return mixed
    */
    public static function get(string $key)
    {
        if (!\array_key_exists($key, $_SERVER)) {
            throw new ConfigurationException(\sprintf('Key not found: "%s".', $key));
        }
        return $_SERVER[$key];
    }

    /**
    * Get an environment configuration value.
    *
    * Key existence and value type are validated.
    */
    public static function getInt(string $key): int
    {
        $value = self::get($key);
        if (!\is_int($value)) {
            throw new ConfigurationException(\sprintf('Value for key "%s" is not an integer', $key));
        }
        return $value;
    }

    /**
    * Get an environment configuration value.
    *
    * Key existence and value type are validated.
    */
    public static function getString(string $key): string
    {
        $value = self::get($key);
        if (!\is_string($value)) {
            throw new ConfigurationException(\sprintf('Value for key "%s" is not a string', $key));
        }
        return $value;
    }
}
