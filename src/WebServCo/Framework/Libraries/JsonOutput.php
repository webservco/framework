<?php
namespace WebServCo\Framework\Libraries;

final class JsonOutput extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\OutputInterface
{
    public function __construct($config)
    {
        parent::__construct($config);
    }
    
    public function setTemplate($template)
    {
        return false;
    }
    
    public function render()
    {
        return json_encode($this->data);
    }
}
