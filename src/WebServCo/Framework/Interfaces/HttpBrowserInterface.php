<?php
namespace WebServCo\Framework\Interfaces;

interface HttpBrowserInterface
{
    public function retrieve(string $url) : \WebServCo\Framework\Http\Response;

    public function setDebug(bool $debug) : bool;

    public function setMethod(string $method) : bool;

    public function setRequestContentType(string $contentType) : bool;

    /**
    * @param array<mixed>|string $data
    * @return bool
    */
    public function setRequestData($data) : bool;

    public function setRequestHeader(string $name, string $value) : bool;
}
