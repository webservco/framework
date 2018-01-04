<?php
namespace WebServCo\Framework;

final class Framework
{
    /**
     * Stores all object instances.
     */
    private static $libraries;
    
    /**
     * Returns an instance of a Framework Library.
     *
     * @param string $className
     *
     * @return mixed
     */
    final private static function get($className)
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
                $pathProject = self::config()->get('app.path.project');
                if (!empty($pathProject)) {
                    $config = self::config()->load($className, $pathProject);
                }
            }
            
            /**
             * Load class.
             */
            self::$libraries[$fullClassName] = new $fullClassName($config);
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
     * Method to access a single instance of the Config Library.
     */
    final public static function config()
    {
        return self::get('Config');
    }
    
    /**
     * Method to access a single instance of the Log Library.
     */
    final public static function log()
    {
        return self::get('Log');
    }
}
