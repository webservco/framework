<?php

declare(strict_types=1);

namespace WebServCo\Framework\Environment;

use WebServCo\Framework\Exceptions\ConfigurationException;

use function is_readable;
use function parse_ini_file;
use function sprintf;

use const INI_SCANNER_TYPED;

final class Settings
{
    protected const string FILENAME = '.env.ini';

    public static function load(string $projectPath): bool
    {
        $filePath = sprintf('%sconfig/%s', $projectPath, self::FILENAME);
        if (!is_readable($filePath)) {
            throw new ConfigurationException('Environment configuration file is not readable.');
        }
        $data = parse_ini_file(
            // filename
            $filePath,
            // true => "multidimensional array, with the section names and settings included"
            // process_sections
            false,
            // \INI_SCANNER_TYPED - tries to convert booleans and numeric types
            // INI_SCANNER_NORMAL - everything is a string
            // scanner_mode
            INI_SCANNER_TYPED,
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
