<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework\Http;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Http\Method;

final class MethodTest extends TestCase
{
    #[Test]
    public function getMethodsReturnsArray(): void
    {
        $this->assertIsArray(Method::getSupported());
    }
}
