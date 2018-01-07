<?php
namespace WebServCo\Framework\Libraries;

final class Config extends \WebServCo\Framework\AbstractLibrary
{
    /**
     * Stores configuration data.
     */
    private $config = [];
    
    /**
     * Application environment.
     */
    private $env;
    
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
     *                          (eg 'app|path|project').
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    final public function set($setting, $value)
    {
        /*
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
        */
        //XXX
        if (empty($setting)) {
            return false;
        }
        $this->config = \WebServCo\Framework\ArrayStorage::set($this->config, $setting, $value);
        return true;
    }
    
    final public function get($setting, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get($this->config, $setting, $defaultValue);
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
