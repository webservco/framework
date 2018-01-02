<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework;

final class FrameworkTest extends TestCase
{
    public function testCLICheckReturnsBoolean()
    {
        $this->assertInternalType('bool', Framework::isCLI());
    }
    
    public function testConfigMethodReturnsCorrectInstance()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Config', Framework::config());
    }
    
    public function testLogMethodReturnsCorrectInstance()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Log', Framework::log());
    }
}
