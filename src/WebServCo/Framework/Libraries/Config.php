<?php
namespace WebServCo\Framework\Libraries;

final class Config extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\ConfigInterface
{
    /**
     * Stores configuration data.
     * @var array<mixed>
     */
    private array $config = [];

    /**
     * Application environment.
     */
    private string $env = '';

    /**
     * Add base setting data.
     *
     * Keys will be preserved.
     * Existing values will be overwritten.
     *
     * @param string $setting Name of setting to load.
     * @param mixed $data Data to add.
     * @return bool
     */
    public function add(string $setting, $data) : bool
    {
        $this->config = \WebServCo\Framework\ArrayStorage::append(
            $this->config,
            [$setting => $data]
        );
        return true;
    }

    /**
     *
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($setting, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get($this->config, $setting, $defaultValue);
    }

    /**
     * Get application environment value.
     *
     * @return string
     */
    public function getEnv() : string
    {
        return $this->env ?: \WebServCo\Framework\Environment::DEV;
    }

    /**
     * Load configuration data from a file.
     *
     * @param string $setting Name of setting to load.
     * @param string $pathProject Directory where the file is located.
     *                      File name must be <$setting>.php
     * @return array<mixed>
     */
    public function load(string $setting, string $pathProject) : array
    {
        $pathFull = "{$pathProject}config/".$this->getEnv()."/{$setting}.php";
        if (!is_readable($pathFull)) {
            return [];
        }
        $data = (include $pathFull);
        return is_array($data) ? $data : [];
    }

    /**
     * Sets a configuration value.
     *
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     *
     * @return bool true on success and false on failure.
     */
    public function set($setting, $value) : bool
    {
        if (empty($setting)) {
            return false;
        }
        $this->config = \WebServCo\Framework\ArrayStorage::set($this->config, $setting, $value);
        return true;
    }

    /**
     * Set application environment value.
     *
     * @param string $env
     *
     * @return bool
     */
    public function setEnv(string $env) : bool
    {
        if (in_array($env, \WebServCo\Framework\Environment::getOptions())) {
            $this->env = $env;
        } else {
            $this->env = \WebServCo\Framework\Environment::DEV;
        }

        return true;
    }
}
