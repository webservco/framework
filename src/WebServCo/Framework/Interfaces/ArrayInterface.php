<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface ArrayInterface
{

    /**
    * @return array<string,mixed>
    */
    public function toArray(): array;
}
