<?php

declare(strict_types=1);

namespace WebServCo\Framework\Environment;

use WebServCo\Framework\Exceptions\ConfigurationException;
use WebServCo\Framework\Exceptions\ConfigurationValidationException;

use function array_key_exists;
use function is_bool;
use function is_int;
use function is_string;
use function sprintf;

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
        if (!is_bool($value)) {
            throw new ConfigurationValidationException(sprintf('Value type for key "%s" is not valid', $key));
        }

        return $value;
    }

    /**
    * Get an environment configuration value.
    *
    * Key existence is validated.
     */
    public static function key(string $key): mixed
    {
        if (!array_key_exists($key, $_SERVER)) {
            throw new ConfigurationException(sprintf('Key not found: "%s".', $key));
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
        if (!is_int($value)) {
            throw new ConfigurationValidationException(sprintf('Value type for key "%s" is not valid', $key));
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
        if (!is_string($value)) {
            throw new ConfigurationValidationException(sprintf('Value type for key "%s" is not valid', $key));
        }

        return $value;
    }
}
