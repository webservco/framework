<?php
namespace WebServCo\Framework\Libraries;

final class Request extends \WebServCo\Framework\AbstractRequest
{
    use \WebServCo\Framework\Traits\RequestProcessTrait;
    use \WebServCo\Framework\Traits\RequestServerTrait;
    use \WebServCo\Framework\Traits\RequestUrlTrait;
    
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
