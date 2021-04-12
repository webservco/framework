<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

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
     *                          (eg 'i18n/lang').
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($setting, $defaultValue = null)
    {
        return \WebServCo\Framework\ArrayStorage::get($this->config, $setting, $defaultValue);
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
        $pathFull = \sprintf('%sconfig/%s.php', $pathProject, $setting);
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
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     * @return bool true on success and false on failure.
     */
    public function set($setting, $value): bool
    {
        if (!$setting) {
            return false;
        }
        $this->config = \WebServCo\Framework\ArrayStorage::set($this->config, $setting, $value);
        return true;
    }
}
