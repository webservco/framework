<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\Interfaces\ArrayInterface;
use WebServCo\Framework\Interfaces\DataInterface;
use WebServCo\Framework\Interfaces\LibraryInterface;
use WebServCo\Framework\Interfaces\SettingsInterface;

abstract class AbstractLibrary implements
    ArrayInterface,
    DataInterface,
    LibraryInterface,
    SettingsInterface
{
    /**
     * Data.
     *
     * @var array<mixed>
     */
    private array $data;

    /**
     * Settings.
     *
     * @var array<string,string|array<mixed>>
     */
    private array $settings;

    final public function clearData(): bool
    {
        $this->data = [];

        return true;
    }

    /**
     * Returns data if exists, $defaultValue otherwise.
     */
    final public function data(string $key, mixed $defaultValue = null): mixed
    {
        return ArrayStorage::get($this->data, $key, $defaultValue);
    }

    /**
     * Returns data if not empty, $defaultValue otherwise.
     * $this->data returns data if it exists (can be empty).
     * This method returns data if both exists and not empty.
     */
    final public function dataElse(string $key, mixed $defaultValue = null): mixed
    {
        return ArrayStorage::getElse($this->data, $key, $defaultValue);
    }

    /**
    * @return array<mixed>
    */
    final public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     * @return bool True on success and false on failure.
     */
    final public function setData(mixed $key, mixed $value): bool
    {
        if (!$key) {
            return false;
        }
        $this->data = ArrayStorage::set($this->data, $key, $value);

        return true;
    }

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     * @return bool True on success and false on failure.
     */
    final public function setSetting(mixed $key, mixed $value): bool
    {
        if (!$key) {
            return false;
        }
        $this->settings = ArrayStorage::set($this->settings, $key, $value);

        return true;
    }

    /**
    * @param mixed $key Can be an array, a string,
    *                          or a special formatted string
    *                          (eg 'i18n/lang').
    */
    final public function setting(mixed $key, mixed $defaultValue = null): mixed
    {
        return ArrayStorage::get($this->settings, $key, $defaultValue);
    }

    /**
    * @param array<string,string|array<mixed>> $settings
    */
    public function __construct(array $settings = [])
    {
        $this->clearData();

        $this->settings = $settings;
    }

    /**
    * @return array<string, array<mixed>>
    */
    public function toArray(): array
    {
        return [
            'data' => $this->data,
            'settings' => $this->settings,
        ];
    }
}
