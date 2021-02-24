<?php

declare(strict_types=1);

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;

final class FrameworkTest extends TestCase
{

    /**
     * @test
     */
    public function getPathReturnsString(): void
    {
        $this->assertIsString(Fw::getPath());
    }
}
