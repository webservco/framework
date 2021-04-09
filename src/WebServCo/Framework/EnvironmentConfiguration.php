<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\Exceptions\ConfigurationException;

final class EnvironmentConfiguration
{
    protected const FILENAME = '.env.ini';

    public static function load(string $path): bool
    {
        if (!\is_readable($path . self::FILENAME)) {
            throw new ConfigurationException('Environment configuration file is not readable.');
        }
        $data = \parse_ini_file(
            $path . self::FILENAME, // filename
            // true => "multidimensional array, with the section names and settings included"
            false, // process_sections
            // \INI_SCANNER_TYPED - tries to convert booleans and numeric types
            // INI_SCANNER_NORMAL - everything is a string
            \INI_SCANNER_TYPED, // scanner_mode
        );
        if (!$data) {
            throw new ConfigurationException('Error loading environment configuration file');
        }
        foreach ($data as $key => $value) {
            $name = \sprintf('APP_%s', \strtoupper($key));

            switch ($name) {
                case 'APP_ENVIRONMENT':
                    self::validateEnvironment($value);
                    break;
                default:
                    break;
            }
            $_SERVER[$name] = $value;
        }
        return true;
    }

    public static function validateEnvironment(string $value): bool
    {
        if (
            !\in_array(
                $value,
                [Environment::DEVELOPMENT, Environment::TESTING, Environment::STAGING, Environment::PRODUCTION],
                true,
            )
        ) {
            throw new ConfigurationException('Invalid environment value.');
        }
        return true;
    }
}
