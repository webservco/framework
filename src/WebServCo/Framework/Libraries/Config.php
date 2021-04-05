<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\Environment;

final class Config extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\ConfigInterface
{

    /**
     * Stores configuration data.
     *
     * @var array<mixed>
     */
    private array $config = [];

    /**
     * Application environment.
     */
    private ?string $env = null;

    /**
     * Add base setting data.
     *
     * Keys will be preserved.
     * Existing values will be overwritten.
     *
     * @param string $setting Name of setting to load.
     * @param mixed $data Data to add.
     */
    public function add(string $setting, $data): bool
    {
        $this->config = \WebServCo\Framework\ArrayStorage::append(
            $this->config,
            [$setting => $data],
        );
        return true;
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($setting, $defaultValue = null)
    {
        return \WebServCo\Framework\ArrayStorage::get($this->config, $setting, $defaultValue);
    }

    /**
     * Get application environment value.
     */
    public function getEnv(): string
    {
        if (!$this->env) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException('Environment not set.');
        }
        return $this->env;
    }

    /**
     * Load configuration data from a file.
     *
     * @param string $setting Name of setting to load.
     * @param string $pathProject Directory where the file is located.
     *                      File name must be <$setting>.php
     * @return array<mixed>
     */
    public function load(string $setting, string $pathProject): array
    {
        $pathFull = "{$pathProject}config/" . $this->getEnv() . "/{$setting}.php";
        if (!\is_readable($pathFull)) {
            return [];
        }
        $data = (include $pathFull);
        return \is_array($data) ? $data : [];
    }

    /**
     * Sets a configuration value.
     *
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     * @return bool true on success and false on failure.
     */
    public function set($setting, $value): bool
    {
        if (empty($setting)) {
            return false;
        }
        $this->config = \WebServCo\Framework\ArrayStorage::set($this->config, $setting, $value);
        return true;
    }

    /**
     * Set application environment value.
     */
    public function setEnv(string $env): bool
    {
        if (
            !\in_array($env, [Environment::DEV, Environment::TEST, Environment::STAGING, Environment::PRODUCTION], true)
        ) {
            throw new \InvalidArgumentException('Invalid environment specified.');
        }

        $this->env = $env;

        return true;
    }
}
