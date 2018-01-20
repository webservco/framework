<?php
namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;

final class FrameworkTest extends TestCase
{
    
    /**
     * @test
     */
    public function getPathReturnsString()
    {
        $this->assertInternalType('string', Fw::getPath());
    }
    
    /**
     * @test
     */
    public function getCliReturnsBoolean()
    {
        $this->assertInternalType('bool', Fw::isCLI());
    }
    
    /**
     * @test
     */
    public function getOsReturnsString()
    {
        $this->assertInternalType('string', Fw::getOS());
    }
    
    /**
     * @test
     */
    public function configMethodReturnsCorrectInstance()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Config', Fw::config());
    }
}
