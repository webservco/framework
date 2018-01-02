<?php
namespace WebServCo\Framework;

final class Framework
{
    private static $libraries;
    
    /**
     * Returns an instance of a Framework Library.
     */
    final private static function get($className)
    {
        /**
         * Work only with framework libraries.
         */
        $className = __NAMESPACE__ . "\\Libraries\\$className";
        /**
         * Load class only if not already loaded.
         */
        if (!isset(self::$libraries[$className])) {
            /**
             * Check if the class exists.
             */
            if (!class_exists($className)) {
                return false;
            }
            /**
             * Load class.
             */
            self::$libraries[$className] = new $className();
        }
        /**
         * Return class instance
         */
        return self::$libraries[$className];
    }
    
    final public static function isCLI()
    {
        return 'cli' === PHP_SAPI;
    }
    
    final public static function config()
    {
        return self::get('Config');
    }
    
    final public static function log()
    {
        return self::get('Log');
    }
}
