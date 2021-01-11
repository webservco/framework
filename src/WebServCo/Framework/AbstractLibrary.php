<?php
namespace WebServCo\Framework;

abstract class AbstractLibrary implements
    \WebServCo\Framework\Interfaces\ArrayInterface,
    \WebServCo\Framework\Interfaces\DataInterface,
    \WebServCo\Framework\Interfaces\LibraryInterface,
    \WebServCo\Framework\Interfaces\SettingsInterface
{
    /**
    * @var array<mixed>
    */
    private array $data;

    /**
    * @var array<string,string|array<mixed>>
    */
    private array $settings;

    /**
    * @param array<string,string|array<mixed>> $settings
    */
    public function __construct(array $settings = [])
    {
        $this->clearData();

        $this->settings = $settings;
    }

    final public function clearData() : bool
    {
        $this->data = [];
        return true;
    }

    /**
     * Returns data if exists, $defaultValue otherwise.
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    final public function data($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get(
            $this->data,
            $key,
            $defaultValue
        );
    }

    /**
     * Returns data if not empty, $defaultValue otherwise.
     * $this->data returns data if it exists (can be empty).
     * This method returns data if both exists and not empty.
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    final public function dataElse($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::getElse(
            $this->data,
            $key,
            $defaultValue
        );
    }

    /**
    * @return array<mixed>
    */
    final public function getData() : array
    {
        return $this->data;
    }

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    final public function setData($key, $value) : bool
    {
        if (empty($key)) {
            return false;
        }
        $this->data = \WebServCo\Framework\ArrayStorage::set($this->data, $key, $value);
        return true;
    }

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    final public function setSetting($key, $value)
    {
        if (empty($key)) {
            return false;
        }
        $this->settings = \WebServCo\Framework\ArrayStorage::set($this->settings, $key, $value);
        return true;
    }

    /**
    * @param mixed $key Can be an array, a string,
    *                          or a special formatted string
    *                          (eg 'app/path/project').
    * @param mixed $defaultValue
    * @return mixed
    */
    final public function setting($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get(
            $this->settings,
            $key,
            $defaultValue
        );
    }

    /**
    * @return array<string, array<mixed>>
    */
    public function toArray() : array
    {
        return [
            'data' => $this->data,
            'settings' => $this->settings,
        ];
    }
}
