<?php
namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;

final class FrameworkTest extends TestCase
{

    /**
     * @test
     */
    public function getPathReturnsString() : void
    {
        $this->assertInternalType('string', Fw::getPath());
    }

    /**
     * @test
     */
    public function getCliReturnsBoolean() : void
    {
        $this->assertInternalType('bool', Fw::isCli());
    }

    /**
     * @test
     */
    public function getOsReturnsString() : void
    {
        $this->assertInternalType('string', Fw::getOS());
    }
}
