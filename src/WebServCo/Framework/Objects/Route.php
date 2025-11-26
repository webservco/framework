<?php

declare(strict_types=1);

namespace WebServCo\Framework\Objects;

final class Route
{
    /**
    * Arguments.
    *
    * @var array<int,string>
    */
    // @phpcs:ignore SlevomatCodingStandard.Classes.ForbiddenPublicProperty.ForbiddenPublicProperty
    public array $arguments;

    /**
    * @param array<int,string> $arguments
    */
    public function __construct(public string $class, public string $method, array $arguments)
    {
        $this->arguments = $arguments;
    }
}
