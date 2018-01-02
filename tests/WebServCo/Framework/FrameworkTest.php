<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Constraint\IsType;
use WebServCo\Framework\Framework;

final class FrameworkTest extends TestCase
{
    public function testCLICheckReturnsBoolean()
    {
        $this->assertInternalType(IsType::TYPE_BOOL, Framework::isCLI());
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
