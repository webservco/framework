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
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Date', Fw::date());
    }
    
    /**
     * @test
     */
    public function setValidTimezoneReturnsTrue()
    {
        $this->assertTrue(Fw::date()->setTimezone('Europe/Budapest'));
    }
    
    /**
     * @test
     */
    public function setInValidTimezoneReturnsFalse()
    {
        $this->assertFalse(Fw::date()->setTimezone('foo/bar'));
    }
    
    /**
     * @test
     */
    public function setTimezoneWithoutParametersUsesDefaultValue()
    {
        $this->assertTrue(Fw::date()->setTimezone());
        $this->assertEquals('Europe/Rome', Fw::date()->getTimezone());
    }
    
    /**
     * @test
     */
    public function getTimezoneWithoutSettingReturnsDefaultValue()
    {
        $this->assertEquals('Europe/Rome', Fw::date()->getTimezone());
    }
   
    /**
     * @test
     */
    public function getTimezoneReturnsString()
    {
        $this->assertInternalType('string', Fw::date()->getTimezone());
    }
}
