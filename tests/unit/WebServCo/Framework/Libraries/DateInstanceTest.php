<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Date;

final class DateInstanceTest extends TestCase
{
    private Date $object;

    public function setUp() : void
    {
        $this->object = new Date();
    }

    /**
     * @test
     */
    public function canBeInstantiatedIndividually() : void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Date',
            $this->object
        );
    }

    /**
     * @test
     */
    public function setValidTimezoneReturnsTrue() : void
    {
        $this->assertTrue($this->object->setTimezone('Europe/Budapest'));
    }

    /**
     * @test
     */
    public function setInValidTimezoneFails() : void
    {
        $this->assertFalse($this->object->setTimezone('foo/bar'));
    }

    /**
     * @test
     */
    public function setTimezoneWithoutParametersUsesDefaultValue() : void
    {
        $this->assertTrue($this->object->setTimezone());
        $this->assertEquals('Europe/Rome', $this->object->getTimezone());
    }

    /**
     * @test
     */
    public function getTimezoneWithoutSettingReturnsDefaultValue() : void
    {
        $this->assertEquals('Europe/Rome', $this->object->getTimezone());
    }

    /**
     * @test
     */
    public function setTimezoneWithoutParametersUsesDefaultValueFromConfig() : void
    {
        $date = new Date(['timezone' => 'Europe/Budapest']);
        $date->setTimezone();
        $this->assertEquals('Europe/Budapest', $date->getTimezone());
    }

    /**
     * @test
     */
    public function getTimezoneReturnsString() : void
    {
        $this->assertInternalType('string', $this->object->getTimezone());
    }
}
