<?php
namespace WebServCo\Framework;

final class Framework
{
    const OS_WINDOWS = 'Windows';
    const OS_LINUX = 'Linux';
    const OS_UNSUPPORTED = 'Unsupported';
    
    /**
     * Stores library instances.
     */
    private static $libraries = [];
    
    private static function getFullClassName($className, $classType)
    {
        switch ($classType) {
            case 'Library':
                return __NAMESPACE__ . '\\Libraries\\' . $className;
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
    
    private static function loadLibraryConfiguration($className)
    {
        if ('Config' == $className) {
            return false;
        }
        $projectPath = self::getLibrary('Config')->get(
            sprintf(
                'app%1$spath%1$sproject',
                \WebServCo\Framework\Settings::DIVIDER
            )
        );
        if (empty($projectPath)) {
            return false;
        }
        return self::getLibrary('Config')->load($className, $projectPath);
    }
    
    private static function loadLibrary($className, $fullClassName, $args = [])
    {
        if (!class_exists($fullClassName)) {
            throw new \ErrorException(
                sprintf('Library %s not found', $fullClassName)
            );
        }
        $config = self::loadLibraryConfiguration($className);
        /**
         * Libraries can have custom parameters to constructor,
         * however the configuration array is always the first.
         */
        $args = is_array($args) ? array_merge([$config], $args) : [$config];
        
        $reflection = new \ReflectionClass($fullClassName);
        return $reflection->newInstanceArgs($args);
    }
    
    public static function getLibrary($className, $args = [], $storageKey = null)
    {
        $fullClassName = self::getFullClassName($className, 'Library');
        
        $storageKey = $storageKey ?: $fullClassName;
        
        if (!isset(self::$libraries[$storageKey])) {
            self::$libraries[$storageKey] = self::loadLibrary($className, $fullClassName, $args);
        }
        
        return self::$libraries[$storageKey];
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
    
    public static function config()
    {
        return self::getLibrary('Config');
    }
    
    public static function request()
    {
        return self::getLibrary('Request', [$_SERVER, $_POST]);
    }
    
    public static function response()
    {
        return self::getLibrary('Response');
    }
    
    public static function router()
    {
        return self::getLibrary('Router');
    }
}
