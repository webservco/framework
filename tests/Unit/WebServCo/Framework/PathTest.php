<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Path;

final class PathTest extends TestCase
{
    /**
     * @test
     */
    public function getPathReturnsString(): void
    {
        $this->assertIsString(Path::get());
    }
}
