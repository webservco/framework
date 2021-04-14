<?php

declare(strict_types=1);

namespace WebServCo\Framework\Environment;

use WebServCo\Framework\Exceptions\ConfigurationException;

final class Config
{
    /**
    * Get an environment configuration value.
    *
    * Key existence and value type are validated.
    */
    public static function bool(string $key): bool
    {
        $value = self::key($key);
        if (!\is_bool($value)) {
            throw new ConfigurationException(\sprintf('Value type for key "%s" is not valid', $key));
        }
        return $value;
    }

    /**
    * Get an environment configuration value.
    *
    * Key existence is validated.
    *
    * @return mixed
    */
    public static function key(string $key)
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
    public static function int(string $key): int
    {
        $value = self::key($key);
        if (!\is_int($value)) {
            throw new ConfigurationException(\sprintf('Value type for key "%s" is not valid', $key));
        }
        return $value;
    }

    /**
    * Get an environment configuration value.
    *
    * Key existence and value type are validated.
    */
    public static function string(string $key): string
    {
        $value = self::key($key);
        if (!\is_string($value)) {
            throw new ConfigurationException(\sprintf('Value type for key "%s" is not valid', $key));
        }
        return $value;
    }
}