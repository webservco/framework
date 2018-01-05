<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Date;

final class DateInstanceTest extends TestCase
{
    private $object;
    
    public function setUp()
    {
        $this->object = new Date();
    }
    
    /**
     * @test
     */
    public function canBeInstantiatedIndividually()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Date',
            $this->object
        );
    }
        
    /**
     * @test
     */
    public function setValidTimezoneReturnsTrue()
    {
        $this->assertTrue($this->object->setTimezone('Europe/Budapest'));
    }
    
    /**
     * @test
     */
    public function setInValidTimezoneFails()
    {
        $this->assertFalse($this->object->setTimezone('foo/bar'));
    }
    
    /**
     * @test
     */
    public function setTimezoneWithoutParametersUsesDefaultValue()
    {
        $this->assertTrue($this->object->setTimezone());
        $this->assertEquals('Europe/Rome', $this->object->getTimezone());
    }
    
    /**
     * @test
     */
    public function getTimezoneWithoutSettingReturnsDefaultValue()
    {
        $this->assertEquals('Europe/Rome', $this->object->getTimezone());
    }
    
    /**
     * @test
     */
    public function setTimezoneWithoutParametersUsesDefaultValueFromConfig()
    {
        $date = new Date(['timezone' => 'Europe/Budapest']);
        $date->setTimezone();
        $this->assertEquals('Europe/Budapest', $date->getTimezone());
    }
    
    /**
     * @test
     */
    public function getTimezoneReturnsString()
    {
        $this->assertInternalType('string', $this->object->getTimezone());
    }
}
