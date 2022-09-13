<?php

namespace WebServCo\Framework\Json;

class Data implements \WebServCo\Framework\Interfaces\JsonInterface
{
    protected $data;

    public function __construct($data = [])
    {
        $this->data = $data;
    }

    public function toJson()
    {
        return json_encode($this->data);
    }
}
