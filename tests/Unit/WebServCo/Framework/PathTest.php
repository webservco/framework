<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Path;

final class PathTest extends TestCase
{
    #[Test]
    public function getPathReturnsString(): void
    {
        $this->assertIsString(Path::get());
    }
}
