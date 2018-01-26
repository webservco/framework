<?php
namespace WebServCo\Framework;

final class ArrayStorage
{
    /**
     * Parse the setting key to make sure it's a simple string
     * or an array.
     */
    private static function parseSetting($setting)
    {
        if (is_string($setting) &&
        false !== strpos($setting, \WebServCo\Framework\Settings::DIVIDER)) {
            return explode(\WebServCo\Framework\Settings::DIVIDER, $setting);
        }
        return $setting;
    }
    
    /**
     * Retrieve a value from a storage array.
     *
     * @param array $storage
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app|path|project').
     * @param mixed $defaultValue
     * @return mixed
     */
    public static function get($storage = [], $setting = null, $defaultValue = false)
    {
        $setting = self::parseSetting($setting);
        
        if (empty($setting) || empty($storage)) {
            return $defaultValue;
        }
        
        /**
         * If $setting is an array, process it recursively.
         */
        if (is_array($setting)) {
            /**
             * Check if we have the first $setting element in the
             * configuration data array.
             */
            if (array_key_exists(0, $setting) && array_key_exists($setting[0], $storage)) {
                /**
                 * Remove first element from $setting.
                 */
                $key = array_shift($setting);
                /**
                 * At the end of the recursion $setting will be
                 * an empty array. In this case we simply return the
                 * current configuration data.
                 */
                if (empty($setting)) {
                    return $storage[$key];
                }
                /**
                 * Go down one lement in the configuration data
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
        if (array_key_exists($setting, $storage)) {
            return $storage[$setting];
        }
        
        /**
         * If we got this far, there is no data to return.
         */
        return $defaultValue;
    }
    
    /**
     * Sets a value in a storage array.
     *
     * @param array $storage
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app|path|project').
     * @param mixed $value The value to be stored.
     *
     * @return array The storage array with new data.
     */
    public static function set($storage, $setting, $value)
    {
        if (!is_array($storage) || empty($setting)) {
            return $storage;
        }
        $setting = self::parseSetting($setting);
        if (is_array($setting)) {
            $reference = &$storage;
            foreach ($setting as $item) {
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
     * Append data to a storage array.
     *
     * @param array $storage
     * @param mixed $data
     * @return array
     */
    public static function append($storage, $data = [])
    {
        if (is_array($storage) && is_array($data)) {
            foreach ($data as $setting => $value) {
                if (array_key_exists($setting, $storage) &&
                    is_array($storage[$setting]) &&
                    is_array($value)
                ) {
                    $storage[$setting] = self::append($storage[$setting], $value);
                } else {
                    $storage[$setting] = $value;
                }
            }
        }
        return $storage;
    }
}
