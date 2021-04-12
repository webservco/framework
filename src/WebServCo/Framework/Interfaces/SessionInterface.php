<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface SessionInterface
{

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $data
     */
    public function add($setting, $data): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function clear($setting): bool;

    public function destroy(): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($setting, $defaultValue = null);

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function has($setting): bool;

    public function regenerate(): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function remove($setting): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     */
    public function set($setting, $value): bool;

    public function start(string $storagePath = ''): bool;
}
