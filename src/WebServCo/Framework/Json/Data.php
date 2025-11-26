<?php

declare(strict_types=1);

namespace WebServCo\Framework\Json;

use WebServCo\Framework\Interfaces\JsonInterface;

use function json_encode;

/**
* A basic JSON data object.
* Used in Json\Response
*/
final class Data implements JsonInterface
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
        return (string) json_encode($this->data);
    }
}
