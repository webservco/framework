<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework;

final class FrameworkTest extends TestCase
{
    /**
     * @test
     */
    public function cliCheckReturnsBoolean()
    {
        $this->assertInternalType('bool', Framework::isCLI());
    }
    
    /**
     * @test
     */
    public function configMethodReturnsCorrectInstance()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Config', Framework::config());
    }
    
    /**
     * @test
     */
    public function logMethodReturnsCorrectInstance()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Log', Framework::log());
    }
}
