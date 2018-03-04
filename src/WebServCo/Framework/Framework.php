<?php
namespace WebServCo\Framework;

use WebServCo\Framework\Exceptions\ApplicationException;

final class Framework
{
    const OS_WINDOWS = 'Windows';
    const OS_LINUX = 'Linux';
    const OS_UNSUPPORTED = 'Unsupported';

    const TYPE_FRAMEWORK = 'Framework';
    const TYPE_PROJECT = 'Project';

    /**
     * Stores Framework Library instances.
     */
    private static $frameworkLibraries = [];

    /**
     * Stores Project Library instances.
     */
    protected static $projectLibraries = [];

    private static function getFullClassName($className, $classType = null)
    {
        switch ($classType) {
            case self::TYPE_FRAMEWORK:
                return sprintf('\\%s\\Libraries\\%s', __NAMESPACE__, $className);
                break;
            case self::TYPE_PROJECT:
                return sprintf('\\Project\\Libraries\\%s', $className);
                break;
            default:
                return $className;
                break;
        }
    }

    /**
     * Returns the path the framework project is located in.
     *
     * @return string
     */
    public static function getPath()
    {
        return str_replace('src/WebServCo/Framework', '', __DIR__);
    }

    private static function loadLibraryConfiguration($configName)
    {
        if ('Config' == $configName) {
            return false;
        }
        $projectPath = self::library('Config')->get(
            sprintf(
                'app%1$spath%1$sproject',
                \WebServCo\Framework\Settings::DIVIDER
            )
        );
        if (empty($projectPath)) {
            return false;
        }
        return self::library('Config')->load($configName, $projectPath);
    }

    private static function loadLibrary($className, $fullClassName, $configName = null)
    {
        if (!class_exists($fullClassName)) {
            throw new ApplicationException(
                sprintf('Library %s not found', $fullClassName)
            );
        }

        switch ($className) {
            case 'I18n':
                self::loadHelper($className);
                break;
        }

        $configName = $configName ?: $className;
        $config = self::loadLibraryConfiguration($configName);
        /**
         * Libraries can have custom parameters to constructor,
         * however the configuration array is always the first.
         * $args = is_array($args) ? array_merge([$config], $args) : [$config];
         */
        switch ($className) {
            case 'Request':
                $args = [$config, $_SERVER, $_POST];
                break;
            default:
                $args = [$config];
                break;
        }

        $reflection = new \ReflectionClass($fullClassName);
        return $reflection->newInstanceArgs($args);
    }

    protected static function loadHelper($className)
    {
        $path = self::getPath() . 'src/WebServCo/Framework/Helpers/' . $className . 'Helper.php';
        if (!is_readable($path)) {
            throw new ApplicationException(
                sprintf('Helper for %s Library not found', $className)
            );
        }
        require $path;
        return true;
    }

    public static function library($className, $storageKey = null, $configName = null)
    {
        $fullClassName = self::getFullClassName($className, self::TYPE_FRAMEWORK);

        $storageKey = $storageKey ?: $fullClassName;

        if (!isset(self::$frameworkLibraries[$storageKey])) {
            self::$frameworkLibraries[$storageKey] = self::loadLibrary($className, $fullClassName, $configName);
        }

        return self::$frameworkLibraries[$storageKey];
    }

    public static function projectLibrary($className, $storageKey = null, $configName = null)
    {
        $fullClassName = self::getFullClassName($className, self::TYPE_PROJECT);

        $storageKey = $storageKey ?: $fullClassName;

        if (!isset(self::$projectLibraries[$storageKey])) {
            self::$projectLibraries[$storageKey] = self::loadLibrary($className, $fullClassName, $configName);
        }

        return self::$projectLibraries[$storageKey];
    }

    /**
     * Checks if interface type is CLI
     */
    public static function isCLI()
    {
        return 'cli' === PHP_SAPI;
    }

    /**
     * Get operating system (if supported).
     */
    public static function getOS()
    {
        $uname = php_uname('s');
        if (0 === strncasecmp($uname, 'Win', 3)) {
            return self::OS_WINDOWS;
        } elseif (0 === strncasecmp($uname, 'Linux', 5)) {
            return self::OS_LINUX;
        } else {
            return self::OS_UNSUPPORTED;
        }
    }
}
