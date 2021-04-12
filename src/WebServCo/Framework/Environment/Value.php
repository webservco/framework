<?php

declare(strict_types=1);

namespace WebServCo\Framework\Environment;

final class Value
{
    // Development - local
    public const DEVELOPMENT = 'development';

    // Test, QC
    public const TESTING = 'testing';

    // Staging, Model, Pre, Demo
    // Mirror of production environment
    public const STAGING = 'staging';

    // Production, Live
    public const PRODUCTION = 'production';

    public static function validate(string $value): bool
    {
        if (
            !\in_array(
                $value,
                [Value::DEVELOPMENT, Value::TESTING, Value::STAGING, Value::PRODUCTION],
                true,
            )
        ) {
            throw new \WebServCo\Framework\Exceptions\EnvironmentException('Invalid environment value.');
        }
        return true;
    }
}
