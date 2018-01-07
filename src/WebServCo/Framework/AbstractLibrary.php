<?php
namespace WebServCo\Framework;

abstract class AbstractLibrary
{
    private $settings = [];
    private $data = [];
    
    public function __construct($settings = [])
    {
        if (is_array($settings)) {
            $this->settings = $settings;
        }
    }
    
    final public function setting($setting, $defaultValue = false)
    {
        return array_key_exists($setting, $this->settings) ?
        $this->settings[$setting] :
        $defaultValue;
    }
    
    final public function data($key, $defaultValue = false)
    {
        return array_key_exists($key, $this->data) ?
        $this->data[$key] :
        $defaultValue;
    }
}
