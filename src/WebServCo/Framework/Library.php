<?php
namespace WebServCo\Framework;

abstract class Library
{
    private $settings = [];
    
    public function __construct($settings = [])
    {
        if (is_array($settings)) {
            $this->settings = $settings;
        }
    }
    
    /**
     * Returns a library setting value.
     *
     * An optional default value can be specified.
     *
     * @param string $setting
     * @param mixed $defaultValue
     * @return mixed
     */
    final protected function setting($setting, $defaultValue = false)
    {
        return array_key_exists($setting, $this->settings) ?
        $this->settings[$setting] :
        $defaultValue;
    }
}
