<?php
namespace WebServCo\Framework\Libraries;

final class Config extends \WebServCo\Framework\AbstractLibrary
{
    /**
     * Delimiter to use for special configuration strings.
     */
    const DELIMITER = '.';
    
    /**
     * Stores configuration data.
     */
    private $config = [];
    
    /**
     * Application environemnt.
     */
    private $env;
    
    /**
     * Parse the setting key to make sure it's a simple string
     * or an array.
     */
    final private function parseSetting($setting)
    {
        if (is_string($setting) && false !== strpos($setting, self::DELIMITER)) {
            return explode(self::DELIMITER, $setting);
        }
        return $setting;
    }
    
    /**
     * Append data to configuration settings.
     *
     * Used recursively.
     * @param array $config Configuration data to append to.
     * @param mixed $data Data to append.
     * @return array
     */
    final private function append($config, $data)
    {
        if (is_array($config) && is_array($data)) {
            foreach ($data as $setting => $value) {
                if (array_key_exists($setting, $config) &&
                    is_array($config[$setting]) &&
                    is_array($value)
                ) {
                    $config[$setting] = $this->append($config[$setting], $value);
                } else {
                    $config[$setting] = $value;
                }
            }
        }
        return $config;
    }
    
    /**
     * Load configuration data from a file.
     *
     * Data is appended to any existing data.
     * @param string $setting Name of setting to load.
     * @param string $path Directory where the file is located.
     *                      File name must be <$setting>.php
     * @return mixed
     */
    final public function load($setting, $pathProject)
    {
        $pathFull = "{$pathProject}config/".$this->getEnv()."/{$setting}.php";
        if (!is_readable($pathFull)) {
            return false;
        }
        $data = (include $pathFull);
        return is_array($data) ? $data : false;
    }
    
    /**
     * Add base setting data.
     *
     * Keys will be preserved.
     * Existing values will be overwritten.
     *
     * @param string $setting Name of setting to load.
     * @param mixed $data Data to add.
     */
    final public function add($setting, $data)
    {
        $this->config = $this->append($this->config, [$setting => $data]);
        return true;
    }
    
    /**
     * Sets a configuration value.
     *
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app.path.project').
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    final public function set($setting, $value)
    {
        if (empty($setting)) {
            return false;
        }
        $setting = $this->parseSetting($setting, true);
        if (is_array($setting)) {
            $reference = &$this->config;
            foreach ($setting as $item) {
                $reference = &$reference[$item];
            }
            $reference = $value;
            unset($reference);
            return true;
        }
        $this->config[$setting] = $value;
        return true;
    }
    
    /**
     * Gets a configuration value.
     *
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app.path.project').
     * @param array $config Configuration data. It is used a method
     *                          parameter in recursion.
     * @return mixed
     */
    final public function get($setting = null, $config = array())
    {
        $setting = $this->parseSetting($setting, true);
        $config = $config ?: $this->config;
        
        if (empty($setting)) {
            return false;
        }
        
        /**
         * If $setting is an array, process it recursively.
         */
        if (is_array($setting)) {
            /**
             * Check if we have the first $setting element in the
             * configuration data array.
             */
            if (array_key_exists(0, $setting) && array_key_exists($setting[0], $config)) {
                /**
                 * Remove first element from $setting.
                 */
                $key = array_shift($setting);
                /**
                 * At the end of the recursion $setting will be
                 * an empty array. In this case we simply return the
                 * current configuration data.
                 */
                if (empty($setting)) {
                    return $config[$key];
                }
                /**
                 * Go down one lement in the configuration data
                 * and call the method again, with the remainig setting.
                 */
                return $this->get($setting, $config[$key]);
            }
            /**
             * The requested setting doesn't exist in our
             * configuration data array.
             */
            return false;
        }
        
        /**
         * If we arrive here, $setting must be a simple string.
         */
        if (array_key_exists($setting, $config)) {
            return $config[$setting];
        }
        
        /**
         * If we got this far, there is no data to return.
         */
        return false;
    }
    
    /**
     * Set application environment value.
     *
     * @param string $env
     *
     * @return bool
     */
    final public function setEnv($env = null)
    {
        if (in_array($env, \WebServCo\Framework\Environment::getOptions())) {
            $this->env = $env;
        } else {
            $this->env = \WebServCo\Framework\Environment::ENV_DEV;
        }
        
        return true;
    }
    
    /**
     * Get application environment value.
     *
     * @return string
     */
    final public function getEnv()
    {
        return $this->env ?: \WebServCo\Framework\Environment::ENV_DEV;
    }
}
