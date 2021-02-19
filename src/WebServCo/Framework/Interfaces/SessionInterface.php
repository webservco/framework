<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface SessionInterface
{

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $data
     */
    public function add($setting, $data): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     */
    public function clear($setting): bool;

    public function destroy(): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $defaultValue
     * @return mixed
     */
    public function get($setting, $defaultValue = false);

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     */
    public function has($setting): bool;

    public function regenerate(): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     */
    public function remove($setting): bool;

    /**
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     */
    public function set($setting, $value): bool;

    public function start(string $storagePath = ''): bool;
}
