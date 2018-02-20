<?php
namespace WebServCo\Framework\Libraries;

final class Request extends \WebServCo\Framework\AbstractRequest
{
    public function __construct($config, $server, $post = [])
    {
        parent::__construct($config);
        
        $this->init($server, $post);
    }
    
    public function getMethod()
    {
        return $this->method;
    }
    
    public function getTarget()
    {
        return $this->target;
    }
    
    public function getQuery()
    {
        return $this->query;
    }
    
    public function getArgs()
    {
        return $this->args;
    }
    
    public function getSuffix()
    {
        return $this->suffix;
    }
}
