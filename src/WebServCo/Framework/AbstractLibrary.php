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
    
    final public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return true;
    }
    
    final public function setting($setting, $defaultValue = false)
    {
        return array_key_exists($setting, $this->settings) ?
        $this->settings[$setting] :
        $defaultValue;
    }
    
    final public function data($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get(
            $this->data,
            $key,
            $defaultValue
        );
    }
}
