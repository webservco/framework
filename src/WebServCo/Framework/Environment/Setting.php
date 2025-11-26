<?php

declare(strict_types=1);

namespace WebServCo\Framework\Environment;

use WebServCo\Framework\Exceptions\ConfigurationException;
use WebServCo\Framework\Helpers\EnvironmentHelper;
use WebServCo\Framework\Helpers\StringHelper;

use function sprintf;
use function strtoupper;

final class Setting
{
    public static function set(string $key, mixed $value): bool
    {
        $key = self::getValidatedKey($key);
        switch ($key) {
            case 'APP_ENVIRONMENT':
                EnvironmentHelper::validate($value);

                break;
            default:
                break;
        }
        $_SERVER[$key] = $value;

        return true;
    }

    public static function getValidatedKey(string $key): string
    {
        $key = strtoupper($key);
        if (!StringHelper::startsWith($key, 'APP_', false)) {
            throw new ConfigurationException(sprintf('Invalid key name: "%s".', $key));
        }

        return $key;
    }
}
