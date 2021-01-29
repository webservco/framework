<?php
namespace WebServCo\Framework\Interfaces;

interface OutputInterface
{
    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app/path/project').
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    public function setData($key, $value): bool;

    public function setTemplate(string $template): bool;

    public function render(): string;
}
