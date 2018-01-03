<?php
namespace WebServCo\Framework\Libraries;

class Config extends \WebServCo\Framework\Library
{
    /**
     * Delimiter to use for special configuration strings.
     */
    private $delimiter;
    
    /**
     * Stores configuration data.
     */
    private $config = [];
    
    /**
     * Application environemnt.
     */
    private $env;
    
    public function __construct($delimiter = '.')
    {
        $this->delimiter = $delimiter;
    }
    
    /**
     * Parse the setting key to make sure it's a simple string
     * or an array.
     */
    private function parseSetting($setting)
    {
        if (is_string($setting) && false !== strpos($setting, $this->delimiter)) {
            return explode($this->delimiter, $setting);
        }
        return $setting;
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
    public function set($setting, $value)
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
    public function get($setting = null, $config = array())
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
    public function setEnv($env = null)
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
    public function getEnv()
    {
        return $this->env ?: \WebServCo\Framework\Environment::ENV_DEV;
    }
}
