<?php

declare(strict_types=1);

namespace WebServCo\Framework;

use WebServCo\Framework\Exceptions\ArrayStorageException;

final class ArrayStorage
{
    /**
     * Add data to an existing key of a storage array.
     *
     * @param array<mixed> $storage
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $data
     * @return array<mixed>
     * @throws \WebServCo\Framework\Exceptions\ArrayStorageException
     */
    public static function add(array $storage, $setting, $data): array
    {
        if (!\is_array($storage) || empty($setting)) {
            throw new ArrayStorageException('Invalid parameters specified.');
        }
        $setting = self::parseSetting($setting);
        $newData = [$data];
        if (self::has($storage, $setting)) {
            $existingData = self::get($storage, $setting);
            if (!\is_array($existingData)) {
                throw new ArrayStorageException('Invalid existing data type.');
            }
            $newData = \array_merge($existingData, $newData);
        }
        return self::set($storage, $setting, $newData);
    }

    /**
     * Append data to a storage array.
     *
     * @param array<mixed> $storage
     * @param mixed $data
     * @return array<mixed>
     * @throws \WebServCo\Framework\Exceptions\ArrayStorageException
     */
    public static function append(array $storage, $data = []): array
    {
        if (!\is_array($storage) || !\is_array($data)) {
            throw new ArrayStorageException('Invalid parameters specified.');
        }
        foreach ($data as $setting => $value) {
            $storage[$setting] = \array_key_exists($setting, $storage) &&
                \is_array($storage[$setting]) &&
                \is_array($value)
                 ? self::append($storage[$setting], $value)
                 : $value;
        }
        return $storage;
    }

    /**
     * Retrieve a value from a storage array.
     *
     * @param mixed $storage
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function get($storage, $setting = null, $defaultValue = null): mixed
    {
        $setting = self::parseSetting($setting);

        if (!isset($setting) || \is_bool($setting) || empty($storage)) {
            // use "!isset" for `$setting` and not "empty" (can be 0)
            // `is_bool` check: handle wrong $setting type
            //     prevents: "array_key_exists(): The first argument should be either a string or an integer"
            return $defaultValue;
        }

        if (!\is_array($storage)) {
            return $defaultValue;
        }

        /**
         * If $setting is an array, process it recursively.
         */
        if (\is_array($setting)) {
            /**
             * Check if we have the first $setting element in the
             * configuration data array.
             */
            if (\array_key_exists(0, $setting) && \array_key_exists($setting[0], $storage)) {
                /**
                 * Remove first element from $setting.
                 */
                $key = \array_shift($setting);
                /**
                 * At the end of the recursion $setting will be
                 * an empty array. In this case we simply return the
                 * current configuration data.
                 */
                if (empty($setting)) {
                    return false !== $storage[$key]
                        ? $storage[$key]
                        : $defaultValue;
                }
                /**
                 * Go down one element in the configuration data
                 * and call the method again, with the remainig setting.
                 */
                return self::get($storage[$key], $setting, $defaultValue);
            }
            /**
             * The requested setting doesn't exist in our
             * configuration data array.
             */
            return $defaultValue;
        }

        /**
         * If we arrive here, $setting must be a simple string.
         */
        if (\array_key_exists($setting, $storage)) {
            return $storage[$setting];
        }

        /**
         * If we got this far, there is no data to return.
         */
        return $defaultValue;
    }

    /**
     * Retrieve a value from a storage array.
     *
     * Returns $defaultValue if $setting is empty.
     *
     * @param mixed $storage
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function getElse($storage, $setting = null, $defaultValue = null)
    {
        $data = self::get($storage, $setting, $defaultValue);
        return !empty($data)
            ? $data
            : $defaultValue;
    }

    /**
    * @param mixed $storage
    * @param mixed $setting Can be an array, a string,
    *                          or a special formatted string
    *                          (eg 'i18n/lang').
    */
    public static function has($storage, $setting): bool
    {
        $value = 'WSFW_NOEXIST';
        $check = self::get($storage, $setting, $value);
        return $check !== $value;
    }

    /**
     * Removes a setting from a storage array.
     *
     * @param array<mixed> $storage
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @return array<mixed> The updated storage array.
     * @throws \WebServCo\Framework\Exceptions\ArrayStorageException
     */
    public static function remove(array $storage, $setting): array
    {
        if (!\is_array($storage) || empty($setting)) {
            throw new ArrayStorageException('Invalid parameters specified.');
        }

        $setting = self::parseSetting($setting);

        if (empty($setting)) {
            throw new ArrayStorageException('Empty setting.');
        }

        if (\is_array($setting)) {
            return self::removeByIndex($storage, $setting);
        }
        if (!\array_key_exists($setting, $storage)) {
            throw new ArrayStorageException(
                \sprintf('setting "%s" does not exist in storage object.', $setting),
            );
        }
        unset($storage[$setting]);
        return $storage;
    }

    /**
     * Sets a value in a storage array.
     *
     * @param array<mixed> $storage
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     * @param mixed $value The value to be stored.
     * @return array<mixed> The storage array with new data.
     * @throws \WebServCo\Framework\Exceptions\ArrayStorageException
     */
    public static function set(array $storage, $setting, $value): array
    {
        if (!\is_array($storage) || empty($setting)) {
            throw new ArrayStorageException('Invalid parameters specified.');
        }
        $setting = self::parseSetting($setting);
        if (\is_array($setting)) {
            // phpcs:ignore SlevomatCodingStandard.PHP.DisallowReference.DisallowedAssigningByReference
            $reference = &$storage;
            foreach ($setting as $item) {
                if (!\is_array($reference)) {
                    $reference = [];
                }
                // phpcs:ignore SlevomatCodingStandard.PHP.DisallowReference.DisallowedAssigningByReference
                $reference = &$reference[$item];
            }
            $reference = $value;
            unset($reference);
            return $storage;
        }
        $storage[$setting] = $value;
        return $storage;
    }

    /**
     * Remove index from multi-dimensional array.
     *
     * https://stackoverflow.com/questions/26661828/
     *
     * @param array<mixed> $array
     *   The array to remove the index from.
     * @param array<int,string> $indices
     *   Indexed array containing the indices chain up to the index that should be
     *   removed.
     * @return array<mixed>
     *   The array with the index removed.
     * @throws \WebServCo\Framework\Exceptions\ArrayStorageException
     *   If the index does not exist within the array.
     */
    protected static function removeByIndex(array $array, array $indices): array
    {
        // Create a reference to the original array.
        // phpcs:ignore SlevomatCodingStandard.PHP.DisallowReference.DisallowedAssigningByReference
        $a = &$array;
        // Count all passed indices, remove one because arrays are zero based.
        $c = \count($indices) - 1;
        // Iterate over all passed indices.
        // phpcs:ignore SlevomatCodingStandard.Operators.DisallowIncrementAndDecrementOperators.DisallowedPreIncrementOperator
        for ($i = 0; $i <= $c; ++$i) {
            // Make sure the index to go down for deletion actually exists.
            if (!\array_key_exists($indices[$i], $a)) {
                throw new ArrayStorageException(
                    \sprintf('"%s" does not exist in storage object.', $indices[$i]),
                );
            }
            // This is the target if we reached the last index that was passed.
            if ($i === $c) {
                unset($a[$indices[$i]]);
            } elseif (\is_array($a[$indices[$i]])) {
                // Make sure we have an array to go further down.
                // phpcs:ignore SlevomatCodingStandard.PHP.DisallowReference.DisallowedAssigningByReference
                $a = &$a[$indices[$i]];
            } else {
                throw new ArrayStorageException(
                    \sprintf('"%s" does not exist in storage object.', $indices[$i]),
                );
            }
        }
        return $array;
    }

    /**
     * Parse the setting key to make sure it's a simple string
     * or an array.
     *
     * @param mixed $setting
     * @return mixed
     */
    private static function parseSetting($setting)
    {
        if (\is_string($setting) && false !== \strpos($setting, \WebServCo\Framework\Settings::DIVIDER)) {
            return \explode(\WebServCo\Framework\Settings::DIVIDER, $setting);
        }
        return $setting;
    }
}
