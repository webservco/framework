<?php
namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Http;

final class HttpTest extends TestCase
{
    /**
     * @test
     */
    public function getMethodsReturnsArray()
    {
        $this->assertInternalType('array', Http::getMethods());
    }
}
