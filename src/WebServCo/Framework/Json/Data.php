<?php

declare(strict_types=1);

namespace WebServCo\Framework\Json;

/**
* A basic JSON data object.
* Used in Json\Response
*/
class Data implements \WebServCo\Framework\Interfaces\JsonInterface
{

    /**
    * Data
    *
    * @var array<mixed>
    */
    protected array $data;

    /**
    * @param array<mixed> $data
    */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    public function toJson(): string
    {
        return (string) \json_encode($this->data);
    }
}
