<?php

declare(strict_types=1);

namespace WebServCo\Framework\Json;

class Data implements \WebServCo\Framework\Interfaces\JsonInterface
{
    /**
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
        return (string) json_encode($this->data);
    }
}
