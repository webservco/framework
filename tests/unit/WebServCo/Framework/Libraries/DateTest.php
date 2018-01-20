<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Date;

final class DateTest extends TestCase
{
    /**
     * @test
     */
    public function canBeAccessedViaFramework()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Date', Fw::getLibrary('Date'));
    }
    
    /**
     * @test
     */
    public function setValidTimezoneReturnsTrue()
    {
        $this->assertTrue(Fw::getLibrary('Date')->setTimezone('Europe/Budapest'));
    }
    
    /**
     * @test
     */
    public function setInValidTimezoneReturnsFalse()
    {
        $this->assertFalse(Fw::getLibrary('Date')->setTimezone('foo/bar'));
    }
    
    /**
     * @test
     */
    public function setTimezoneWithoutParametersUsesDefaultValue()
    {
        $this->assertTrue(Fw::getLibrary('Date')->setTimezone());
        $this->assertEquals('Europe/Rome', Fw::getLibrary('Date')->getTimezone());
    }
    
    /**
     * @test
     */
    public function getTimezoneWithoutSettingReturnsDefaultValue()
    {
        $this->assertEquals('Europe/Rome', Fw::getLibrary('Date')->getTimezone());
    }
   
    /**
     * @test
     */
    public function getTimezoneReturnsString()
    {
        $this->assertInternalType('string', Fw::getLibrary('Date')->getTimezone());
    }
}
