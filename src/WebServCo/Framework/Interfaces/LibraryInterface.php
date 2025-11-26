<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface LibraryInterface
{
    /**
     * Returns data if exists, $defaultValue otherwise.
     */
    public function data(string $key, mixed $defaultValue = null): mixed;

    /**
    * @return array<mixed>
    */
    public function getData(): array;

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     * @return bool True on success and false on failure.
     */
    public function setData(mixed $key, mixed $value): bool;

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     * @return bool True on success and false on failure.
     */
    public function setSetting(mixed $key, mixed $value): bool;

    /**
    * @param mixed $key Can be an array, a string,
    *                          or a special formatted string
    *                          (eg 'i18n/lang').
    */
    public function setting(mixed $key, mixed $defaultValue = null): mixed;
}
