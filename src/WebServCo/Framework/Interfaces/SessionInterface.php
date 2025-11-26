<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface SessionInterface
{
    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function add(mixed $setting, mixed $data): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function clear(mixed $setting): bool;

    public function destroy(): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function get(mixed $setting, mixed $defaultValue = null): mixed;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function has(mixed $setting): bool;

    public function regenerate(): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function remove(mixed $setting): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     */
    public function set(mixed $setting, mixed $value): bool;

    public function start(string $storagePath = ''): bool;
}
