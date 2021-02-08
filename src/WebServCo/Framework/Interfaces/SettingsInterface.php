<?php declare(strict_types = 1);

namespace WebServCo\Framework\Interfaces;

interface SettingsInterface
{
    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    public function setSetting($key, $value);

    /**
    * @param mixed $key Can be an array, a string,
    *                          or a special formatted string
    *                          (eg 'app/path/project').
    * @param mixed $defaultValue
    * @return mixed
    */
    public function setting($key, $defaultValue = false);
}
