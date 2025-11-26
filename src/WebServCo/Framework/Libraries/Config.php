<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\AbstractLibrary;
use WebServCo\Framework\ArrayStorage;
use WebServCo\Framework\Interfaces\ConfigInterface;

use function is_array;
use function is_readable;
use function sprintf;

final class Config extends AbstractLibrary implements
    ConfigInterface
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
    public function add(string $setting, mixed $data): bool
    {
        $this->config = ArrayStorage::append(
            $this->config,
            [$setting => $data],
        );

        return true;
    }

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function get(mixed $setting, mixed $defaultValue = null): mixed
    {
        return ArrayStorage::get($this->config, $setting, $defaultValue);
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
        $pathFull = sprintf('%sconfig/%s.php', $pathProject, $setting);
        if (!is_readable($pathFull)) {
            return [];
        }
        $data = (include $pathFull);

        return is_array($data)
            ? $data
            : [];
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
    public function set(mixed $setting, mixed $value): bool
    {
        if (!$setting) {
            return false;
        }
        $this->config = ArrayStorage::set($this->config, $setting, $value);

        return true;
    }
}
