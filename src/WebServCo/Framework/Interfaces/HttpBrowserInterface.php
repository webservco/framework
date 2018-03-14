<?php
namespace WebServCo\Framework\Interfaces;

interface HttpBrowserInterface
{
    public function get($url);

    public function post($url, $data = []);
}
