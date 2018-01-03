<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;

final class FrameworkTest extends TestCase
{
    /**
     * @test
     */
    public function cliCheckReturnsBoolean()
    {
        $this->assertInternalType('bool', Fw::isCLI());
    }
    
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
    public function configMethodReturnsCorrectInstance()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Config', Fw::config());
    }
    
    /**
     * @test
     */
    public function logMethodReturnsCorrectInstance()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Log', Fw::log());
    }
}
