<?php

declare(strict_types=1);

namespace WebServCo\Framework\Environment;

use WebServCo\Framework\Exceptions\ConfigurationException;

final class Settings
{
    protected const string FILENAME = '.env.ini';

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
            Setting::set($key, $value);
        }
        return true;
    }
}
