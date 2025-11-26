<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface ConfigInterface
{
    /**
     * Add base setting data.
     *
     * Keys will be preserved.
     * Existing values will be overwritten.
     *
     * @param string $setting Name of setting to load.
     * @param mixed $data Data to add.
     */
    public function add(string $setting, mixed $data): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function get(mixed $setting, mixed $defaultValue = null): mixed;

    /**
     * Load configuration data from a file.
     *
     * @param string $setting Name of setting to load.
     * @param string $pathProject Directory where the file is located.
     *                      File name must be <$setting>.php
     * @return array<mixed>
     */
    public function load(string $setting, string $pathProject): array;

    /**
     * Sets a configuration value.
     *
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     * @return bool True on success and false on failure.
     */
    public function set(mixed $setting, mixed $value): bool;
}
