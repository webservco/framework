<?php
namespace WebServCo\Framework;

final class Framework
{
    const OS_WINDOWS = 'Windows';
    const OS_LINUX = 'Linux';
    const OS_UNSUPPORTED = 'Unsupported';
    
    /**
     * Stores all object instances.
     */
    private static $libraries = [];
    
    /**
     * Returns an instance of a Framework Library.
     *
     * @param string $className
     *
     * @return mixed
     */
    final private static function get($className, $args = [])
    {
        /**
         * Work only with framework libraries.
         */
        $fullClassName = __NAMESPACE__ . "\\Libraries\\$className";
        /**
         * Load class only if not already loaded.
         */
        if (!isset(self::$libraries[$fullClassName])) {
            /**
             * Check if the class exists.
             */
            if (!class_exists($fullClassName)) {
                return false;
            }
            /**
             * Load class config.
             */
            $config = [];
            if ('Config' !== $className) {
                /**
                 * If the Application library is used, project path is
                 * available, so we can load the custom configuration
                 * file for the library.
                 */
                $pathProject = self::config()->get('app.path.project');
                if (!empty($pathProject)) {
                    $config = self::config()->load($className, $pathProject);
                }
            }
            /**
             * Libraries can have custom parameters to constructor,
             * however the configuration array is always the first.
             */
            $args = is_array($args) ? array_merge([$config], $args) : [$config];
            /**
             * Load class.
             */
            $reflection = new \ReflectionClass($fullClassName);
            self::$libraries[$fullClassName] = $reflection->newInstanceArgs($args);
        }
        /**
         * Return class instance
         */
        return self::$libraries[$fullClassName];
    }
    
    /**
     * Returns the path the framework project is located in.
     *
     * @return string
     */
    final public static function getPath()
    {
        return str_replace('src/WebServCo/Framework', '', __DIR__);
    }
    
    /**
     * Checks if interface type is CLI
     */
    final public static function isCLI()
    {
        return 'cli' === PHP_SAPI;
    }
    
    /**
     * Get operating system (if supported).
     */
    final public static function getOS()
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
    
    final public static function config()
    {
        return self::get('Config');
    }
    
    final public static function log()
    {
        return self::get('Log');
    }
    
    final public static function date()
    {
        return self::get('Date');
    }
    
    final public static function request()
    {
        return self::get('Request', [$_SERVER]);
    }
    
    final public static function router()
    {
        return self::get('Router');
    }
}
