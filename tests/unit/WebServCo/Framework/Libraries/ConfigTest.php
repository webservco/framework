<?php

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Config;

final class ConfigTest extends TestCase
{
    private $settingSimpleString = 'setting';
    private $settingArray = ['setting_array1', 'setting_array2', 'setting_array3'];
    private $settingSpecialString = 'setting1.setting2.setting3';
    private $value = 'value';
    
    /**
     * @test
     */
    public function canBeInstantiatedIndividually()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Config',
            new Config()
        );
    }
    
    /**
     * @test
     */
    public function canBeAccessedViaFramework()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Config', Fw::config());
    }
    
    /**
     * @test
     */
    public function nullSettingReturnsFalse()
    {
        $this->assertFalse(Fw::config()->set(null, null));
    }
    
    /**
     * @test
     */
    public function falseSettingReturnsFalse()
    {
        $this->assertFalse(Fw::config()->set(false, null));
    }
    
    /**
     * @test
     */
    public function emptySettingReturnsFalse()
    {
        $this->assertFalse(Fw::config()->set('', null));
    }
     
    /**
     * @test
     */
    public function validSettingReturnsTrue()
    {
        $this->assertTrue(Fw::config()->set('setting', 'value'));
    }
    
    /**
     * @test
     */
    public function settingNullValueReturnsTrue()
    {
        $this->assertTrue(Fw::config()->set('key', null));
    }
    
    /**
     * @test
     */
    public function settingFalseValueReturnsTrue()
    {
        $this->assertTrue(Fw::config()->set('key', false));
    }
    
    /**
     * @test
     */
    public function settingEmptyValueReturnsTrue()
    {
        $this->assertTrue(Fw::config()->set('key', ''));
    }
    
    /**
     * @test
     * @depends validSettingReturnsTrue
     */
    public function frameworkAccessUsesSingleInstance()
    {
        $this->assertEquals('value', Fw::config()->get('setting'));
    }
    
    /**
     * @test
     */
    public function gettingNonExistentSettingReturnsFalse()
    {
        $this->assertFalse(Fw::config()->get('noexist'));
    }
    
    /**
     * @test
     */
    public function gettingNullSettingReturnsFalse()
    {
        $this->assertFalse(Fw::config()->get(null));
    }
    
    /**
     * @test
     */
    public function gettingFalseSettingReturnsFalse()
    {
        $this->assertFalse(Fw::config()->get(false));
    }
    
    /**
     * @test
     */
    public function gettingEmptySettingReturnsFalse()
    {
        $this->assertFalse(Fw::config()->get(''));
    }
    
    /**
     * @test
     */
    public function gettingEmptyArraySettingReturnsFalse()
    {
        $this->assertFalse(Fw::config()->get([]));
    }
    
    /**
     * @test
     */
    public function storingAndRetrievingSimpleStringSettingWorks()
    {
        $this->assertTrue(Fw::config()->set($this->settingSimpleString, $this->value));
        $this->assertEquals($this->value, Fw::config()->get($this->settingSimpleString));
    }
    
    /**
     * @test
     */
    public function storingAndRetrievingArraySettingWorks()
    {
        $this->assertTrue(Fw::config()->set($this->settingArray, $this->value));
        $this->assertEquals($this->value, Fw::config()->get($this->settingArray));
    }
    
    /**
     * @test
     */
    public function storingAndRetrievingSpecialStringSettingWorks()
    {
        $this->assertTrue(Fw::config()->set($this->settingSpecialString, $this->value));
        $this->assertEquals($this->value, Fw::config()->get($this->settingSpecialString));
    }
    
    /**
     * @test
     */
    public function settingsTreeIsNoOverwrittenOnSpecialStringSetting()
    {
        $this->assertTrue(Fw::config()->set('app.one.sub_two.key', $this->value));
        $this->assertTrue(Fw::config()->set('app.two.sub_two.key', $this->value));
        $this->assertTrue(Fw::config()->set('app.three.sub_three.key', $this->value));
        $this->assertEquals($this->value, Fw::config()->get('app.one.sub_two.key'));
    }
    
    /**
     * @test
     */
    public function settingsTreeIsOverwrittenOnRootKeySimpleStringSetting()
    {
        $this->assertTrue(Fw::config()->set('app.one.sub_two.key', $this->value));
        $this->assertTrue(Fw::config()->set('app.two.sub_two.key', $this->value));
        $this->assertTrue(Fw::config()->set('app.three.sub_three.key', $this->value));
        $this->assertTrue(Fw::config()->set('app', $this->value));
        $this->assertEquals($this->value, Fw::config()->get('app'));
    }
    
    /**
     * @test
     */
    public function setEnvReturnsTrue()
    {
        $this->assertTrue(Fw::config()->setEnv('dev'));
    }
    
    /**
     * @test
     */
    public function setEnvDefaultsToDevOnInvalidValue()
    {
        Fw::config()->setEnv('noexist');
        $this->assertEquals('dev', Fw::config()->getEnv());
    }
    
    /**
     * @test
     */
    public function getEnvReturnsString()
    {
        $this->assertInternalType('string', Fw::config()->getEnv());
    }
    
    /**
     * @test
     */
    public function getEnvDefaultsToDev()
    {
        $config = new \WebServCo\Framework\Libraries\Config;
        $this->assertEquals('dev', $config->getEnv());
    }
}
