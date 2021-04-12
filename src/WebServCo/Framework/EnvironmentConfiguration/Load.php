<?php

declare(strict_types=1);

namespace WebServCo\Framework\EnvironmentConfiguration;

use WebServCo\Framework\Exceptions\ConfigurationException;

final class Load
{
    protected const FILENAME = '.env.ini';

    public static function do(string $projectPath): bool
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
            Set::do($key, $value);
        }
        return true;
    }
}
