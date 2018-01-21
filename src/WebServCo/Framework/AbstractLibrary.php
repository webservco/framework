<?php
namespace WebServCo\Framework;

abstract class AbstractLibrary
{
    private $settings = [];
    protected $data = [];
    
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
    
    final public function setting($key, $defaultValue = false)
    {
        return \WebServCo\Framework\ArrayStorage::get(
            $this->settings,
            $key,
            $defaultValue
        );
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
