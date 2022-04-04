<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Values\Environment;

class EnvironmentHelper
{
    public static function validate(string $value): bool
    {
        if (
            !\in_array(
                $value,
                [Environment::DEVELOPMENT, Environment::TESTING, Environment::STAGING, Environment::PRODUCTION],
                true,
            )
        ) {
            throw new \WebServCo\Framework\Exceptions\EnvironmentException('Invalid environment value.');
        }
        return true;
    }
}
