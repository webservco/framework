<?php declare(strict_types = 1);

namespace Tests\Framework\Http;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Http\Method;

final class MethodTest extends TestCase
{

    /**
     * @test
     */
    public function getMethodsReturnsArray(): void
    {
        $this->assertIsArray(Method::getSupported());
    }
}
