<?php
namespace WebServCo\Framework\Libraries;

final class JsonOutput extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\OutputInterface
{
    final public function __construct($config)
    {
        parent::__construct($config);
    }
    
    final public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return true;
    }
    
    final public function setTemplate($template)
    {
        return false;
    }
    
    final public function render()
    {
        return json_encode($this->data);
    }
}
