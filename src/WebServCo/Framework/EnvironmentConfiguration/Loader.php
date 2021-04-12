<?php

declare(strict_types=1);

namespace WebServCo\Framework\EnvironmentConfiguration;

use WebServCo\Framework\Environment;
use WebServCo\Framework\Exceptions\ConfigurationException;

final class Loader
{
    protected const FILENAME = '.env.ini';

    public static function load(string $projectPath): bool
    {
        $filePath = \sprintf('%sconfig/%s', $projectPath, self::FILENAME);
        if (!\is_readable($filePath)) {
            throw new ConfigurationException('Environment configuration file is not readable.');
        }
        $data = \parse_ini_file(
            $filePath, // filename
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
            self::set($key, $value);
        }
        return true;
    }

    /**
    * @param mixed $value
    */
    public static function set(string $key, $value): bool
    {
        $key = self::getValidatedKey($key);
        switch ($key) {
            case 'APP_ENVIRONMENT':
                self::validateEnvironment($value);
                break;
            default:
                break;
        }
        $_SERVER[$key] = $value;
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

    public static function getValidatedKey(string $key): string
    {
        $key = \strtoupper($key);
        if (!\WebServCo\Framework\Utils\Strings::startsWith($key, 'APP_', false)) {
            throw new ConfigurationException(\sprintf('Invalid key name: "%s".', $key));
        }
        return $key;
    }
}
