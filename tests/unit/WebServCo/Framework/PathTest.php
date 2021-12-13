<?php

declare(strict_types=1);

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;

final class PathTest extends TestCase
{
    /**
     * @test
     */
    public function getPathReturnsString(): void
    {
        $this->assertIsString(\WebServCo\Framework\Path::get());
    }
}
