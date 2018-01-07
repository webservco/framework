<?php
namespace WebServCo\Framework\Libraries;

final class HtmlOutput extends \WebServCo\Framework\AbstractLibrary implements
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
    
    final public function getData($key)
    {
        return $this->data($key, false);
    }
    
    final public function setTemplate($template)
    {
        return false;
    }
    
    final public function render()
    {
        return false;
    }
}
