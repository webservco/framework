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
    public function canBeAccessedViaFramework() : void
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Date', Fw::library('Date'));
    }

    /**
     * @test
     */
    public function setValidTimezoneReturnsTrue() : void
    {
        $this->assertTrue(Fw::library('Date')->setTimezone('Europe/Budapest'));
    }

    /**
     * @test
     */
    public function setInValidTimezoneReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Date')->setTimezone('foo/bar'));
    }

    /**
     * @test
     */
    public function setTimezoneWithoutParametersUsesDefaultValue() : void
    {
        $this->assertTrue(Fw::library('Date')->setTimezone());
        $this->assertEquals('Europe/Rome', Fw::library('Date')->getTimezone());
    }

    /**
     * @test
     */
    public function getTimezoneWithoutSettingReturnsDefaultValue() : void
    {
        $this->assertEquals('Europe/Rome', Fw::library('Date')->getTimezone());
    }

    /**
     * @test
     */
    public function getTimezoneReturnsString() : void
    {
        $this->assertInternalType('string', Fw::library('Date')->getTimezone());
    }
}
