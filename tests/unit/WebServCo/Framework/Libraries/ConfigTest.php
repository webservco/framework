<?php

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Config;

final class ConfigTest extends TestCase
{
    private static $pathProject = '';
   
    private $settingSimpleString = 'setting';
    private $settingArray = ['setting_array1', 'setting_array2', 'setting_array3'];
    private $settingSpecialString = 'setting1.setting2.setting3';
    private $value = 'value';
    
    public static function setUpBeforeClass()
    {
        $pathProject = '/tmp/webservco/project/';
        if (!is_readable($pathProject)) {
                mkdir($pathProject, 0775, true);
                $data = "<?php
                return [
                    'date' => [
                        'timezone' => 'Europe/Budapest',
                    ],
                    'options' => [
                        'setting1' => 'value1',
                        'setting2' => 'value2',
                        'setting3' => 'value3',
                    ],
                    'level1' => [
                        'level2' => [
                            'level3' => ['value']
                        ],
                    ],
                    ];
                ";
                file_put_contents("{$pathProject}foo.php", $data);
        }
        self::$pathProject = $pathProject;
    }

    public static function tearDownAfterClass()
    {
        $pathBase = '/tmp/webservco/';
        $pathProject = "{$pathBase}project/";
        if (is_readable($pathProject)) {
            if (is_readable("{$pathProject}foo.php")) {
                unlink("{$pathProject}foo.php");
            }
            rmdir($pathProject);
            rmdir($pathBase);
        }
    }
    
    public function setUp()
    {
        /**
         * Reset data to prevent phpunit hanging
         */
        Fw::config()->set('app', null);
        Fw::config()->set('foo', null);
    }
    
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
    public function settingSameKeyTwiceOverwritesTheFirst()
    {
        $this->assertTrue(Fw::config()->set('foo', 'old value'));
        $this->assertTrue(Fw::config()->set('foo', 'new value'));
        $this->assertEquals('new value', Fw::config()->get('foo'));
    }
    
    /**
     * @test
     */
    public function settingSameMultilevelKeyTwiceOverwritesTheFirst()
    {
        $this->assertTrue(Fw::config()->set('foo.bar.baz', 'old value'));
        $this->assertTrue(Fw::config()->set('foo.bar.baz', 'new value'));
        $this->assertEquals('new value', Fw::config()->get('foo.bar.baz'));
    }
    
    /**
     * @test
     */
    public function addReturnsTrue()
    {
        $this->assertTrue(Fw::config()->add('add', 'dda'));
    }
    
    /**
     * @test
     */
    public function addAppendsDataInsteadOfOverwriting()
    {
        
        $config = [
            'date' => [
                'timezone' => 'Europe/Budapest',
            ],
            'options' => [
                'setting1' => 'value1',
                'setting2' => 'value2',
                'setting3' => 'value3',
            ],
            'level1' => [
                'level2' => [
                    'level3' => ['value']
                ],
            ],
        ];
        $this->assertTrue(Fw::config()->set('foo.bar.baz', 'old value'));
        $this->assertTrue(Fw::config()->set('foo.bar.baz', 'new value'));
        $this->assertTrue(Fw::config()->add('foo', $config));
        
        $this->assertEquals('value', Fw::config()->get('foo.level1.level2.level3.0'));
        $this->assertEquals('new value', Fw::config()->get('foo.bar.baz'));
    }
    
    /**
     * @test
     */
    public function loadReturnsFalseOnInvalidPath()
    {
        $this->assertFalse(Fw::config()->load('foo', '/foo/bar'));
    }
    
    /**
     * @test
     */
    public function loadReturnsTrueOnValidPath()
    {
        $this->assertTrue(is_readable(self::$pathProject . 'foo.php'));
        $this->assertTrue(Fw::config()->load('foo', self::$pathProject));
    }
    
    /**
     * @test
     */
    public function loadWorks()
    {
        $this->assertTrue(is_readable(self::$pathProject . 'foo.php'));
        $this->assertTrue(Fw::config()->load('foo', self::$pathProject));
        $this->assertEquals('value1', Fw::config()->get('foo.options.setting1'));
    }
    /**
     * @test
     */
    public function loadAppendsDataInsteadOfOverwriting()
    {
        $this->assertTrue(is_readable(self::$pathProject . 'foo.php'));
        $this->assertTrue(Fw::config()->set('foo.bar.baz', 'new value'));
        $this->assertTrue(Fw::config()->load('foo', self::$pathProject));
        $this->assertEquals('new value', Fw::config()->get('foo.bar.baz'));
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
