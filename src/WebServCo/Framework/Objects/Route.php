<?php

declare(strict_types=1);

namespace WebServCo\Framework\Objects;

final class Route
{
    public string $class;

    public string $method;

    /**
    * Arguments.
    *
    * @var array<int,string>
    */
    public array $arguments;

    /**
    * @param array<int,string> $arguments
    */
    public function __construct(string $class, string $method, array $arguments)
    {
        $this->class = $class;
        $this->method = $method;
        $this->arguments = $arguments;
    }
}
