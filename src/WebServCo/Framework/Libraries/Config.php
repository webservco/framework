<?php
namespace WebServCo\Framework\Libraries;

final class Config extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\ConfigInterface
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
     * Add base setting data.
     *
     * Keys will be preserved.
     * Existing values will be overwritten.
     *
     * @param string $setting Name of setting to load.
     * @param mixed $data Data to add.
     */
    public function add($setting, $data)
    {
        $this->config = \WebServCo\Framework\ArrayStorage::append(
            $this->config,
            [$setting => $data]
        );
        return true;
    }

    /**
     * Load configuration data from a file.
     *
     * @param string $setting Name of setting to load.
     * @param string $pathProject Directory where the file is located.
     *                      File name must be <$setting>.php
     * @return mixed
     */
    public function load($setting, $pathProject)
    {
        $pathFull = "{$pathProject}config/".$this->getEnv()."/{$setting}.php";
        if (!is_readable($pathFull)) {
            return false;
        }
        $data = (include $pathFull);
        return is_array($data) ? $data : false;
    }

    /**
     * Sets a configuration value.
     *
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    public function set($setting, $value)
    {
        if (empty($setting)) {
            return false;
        }
        $this->config = \WebServCo\Framework\ArrayStorage::set($this->config, $setting, $value);
        return true;
    }

    public function get($setting, $defaultValue = false)
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
