<?php
namespace WebServCo\Framework\Interfaces;

interface HttpBrowserInterface
{
    public function retrieve($url);
    
    public function setDebug($debug);

    public function setMethod($method);

    public function setRequestContentType($contentType);

    public function setRequestData($data);

    public function setRequestHeader($name, $value);
}
