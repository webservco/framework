<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Exceptions\EnvironmentException;
use WebServCo\Framework\Values\Environment;

use function in_array;

final class EnvironmentHelper
{
    public static function validate(string $value): bool
    {
        $condition = !in_array(
            $value,
            [Environment::DEVELOPMENT, Environment::TESTING, Environment::STAGING, Environment::PRODUCTION],
            true,
        );
        if ($condition) {
            throw new EnvironmentException('Invalid environment value.');
        }

        return true;
    }
}
