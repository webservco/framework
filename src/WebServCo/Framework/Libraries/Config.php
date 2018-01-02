<?php
namespace WebServCo\Framework\Libraries;

class Config extends \WebServCo\Framework\Library
{
    /**
     * Delimiter to use for special configuration strings.
     */
    private $delimiter;
    
    private $config = [];
    
    public function __construct($delimiter = '.')
    {
        $this->delimiter = $delimiter;
    }
    
    /**
     * Parse the setting key to make sure it's a simple string
     * or an array.
     */
    private function parseSetting($setting)
    {
        if (is_string($setting) && false !== strpos($setting, $this->delimiter)) {
            return explode($this->delimiter, $setting);
        }
        return $setting;
    }
    
    /**
     * Sets a configuration value.
     *
     * @param mixed $setting Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'app.path.project')
     * @param mixed $value The value to be stored.
     *
     * @return bool True on success and false on failure.
     */
    public function set($setting, $value)
    {
        if (empty($setting)) {
            return false;
        }
        $setting = $this->parseKey($setting, true);
        if (is_array($setting)) {
            $reference = &$this->config;
            foreach ($setting as $item) {
                $reference = &$reference[$item];
            }
            $reference = $value;
            unset($reference);
            return true;
        }
        $this->config[$setting] = $value;
        return true;
    }
}
