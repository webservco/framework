<?php
namespace WebServCo\Framework\Libraries;

class Config
{
    private $delimiter;
    
    private $config = [];
    
    public function __construct()
    {
        $this->delimiter = '.';
    }
    
}
