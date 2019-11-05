<?php
namespace WebServCo\Framework;

abstract class AbstractLibrary implements
    \WebServCo\Framework\Interfaces\ArrayInterface,
    \WebServCo\Framework\Interfaces\DataInterface,
    \WebServCo\Framework\Interfaces\SettingsInterface
{
    private $settings;
    private $data;

    public function __construct($settings = [])
    {
        $this->clearData();
        $this->settings = (array) $settings;
    }

    final public function clearData()
    {
        $this->data = [];
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

    final public function getData()
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
    final public function setData($key, $value)
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

    final public function setting($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get(
            $this->settings,
            $key,
            $defaultValue
        );
    }

    public function toArray()
    {
        return [
            'data' => $this->data,
            'settings' => $this->settings,
        ];
    }

    /**
     * Returns data if not empty, $defaultValue otherwise.
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    final public function when($key, $defaultValue = false)
    {
        $data = $this->data($key, false);
        return !empty($data) ? $data : $defaultValue;
    }
}
