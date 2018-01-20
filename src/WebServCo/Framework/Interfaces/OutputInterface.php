<?php
namespace WebServCo\Framework\Interfaces;

interface OutputInterface
{
    public function setData($key, $value);
    
    public function setTemplate($template);
    
    public function render();
}
