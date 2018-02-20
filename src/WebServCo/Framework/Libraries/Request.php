<?php
namespace WebServCo\Framework\Libraries;

final class Request extends \WebServCo\Framework\AbstractRequest
{
    public function __construct($settings, $server, $post = [])
    {
        parent::__construct($settings);
        
        $this->init($server, $post);
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getArgs()
    {
        return $this->args;
    }
}
