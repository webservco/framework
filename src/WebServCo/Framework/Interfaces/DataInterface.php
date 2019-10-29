<?php
namespace WebServCo\Framework\Interfaces;

interface DataInterface
{
    public function data($key, $defaultValue = false);
    public function getData();
    public function setData($key, $value);
}
