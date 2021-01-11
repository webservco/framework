<?php
namespace WebServCo\Framework\Interfaces;

interface LibraryInterface
{
    /**
     * Returns data if exists, $defaultValue otherwise.
     *
     * @param string $key
     * @param mixed $defaultValue
     * @return mixed
     */
    public function data($key, $defaultValue = false);

    /**
    * @return array<mixed>
    */
    public function getData() : array;

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    public function setData($key, $value) : bool;

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
