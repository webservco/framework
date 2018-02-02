<?php
namespace WebServCo\Framework\Libraries;

final class HttpResponse extends \WebServCo\Framework\AbstractResponse implements
    \WebServCo\Framework\Interfaces\ResponseInterface
{
    protected $headers;
    
    public function __construct($content = null, $status = 200, $headers = [])
    {
        $this->setContent($content);
        $this->status = $status; //TODO move to method
        $this->headers = $headers; //TODO move to method?
    }
}
