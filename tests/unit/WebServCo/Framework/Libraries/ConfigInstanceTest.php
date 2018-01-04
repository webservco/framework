<?php

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Config;

final class ConfigInstanceTest extends TestCase
{
    private $object;
    
    private static $pathProject = '';
   
    private $settingSimpleString = 'setting';
    private $settingArray = ['setting_array1', 'setting_array2', 'setting_array3'];
    private $settingSpecialString = 'setting1.setting2.setting3';
    private $value = 'value';
    
    public static function setUpBeforeClass()
    {
        $pathProject = '/tmp/webservco/project/';
        $pathConfig = "{$pathProject}config/dev/";
        if (!is_readable($pathConfig)) {
                mkdir($pathConfig, 0775, true);
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
                file_put_contents("{$pathConfig}foo.php", $data);
        }
        self::$pathProject = $pathProject;
    }

    public static function tearDownAfterClass()
    {
        $pathBase = '/tmp/webservco/';
        $it = new \RecursiveDirectoryIterator(
            $pathBase,
            \RecursiveDirectoryIterator::SKIP_DOTS
        );
        $files = new \RecursiveIteratorIterator(
            $it,
            \RecursiveIteratorIterator::CHILD_FIRST
        );
        foreach ($files as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }
        rmdir($pathBase);
    }
    
    public function setUp()
    {
        $this->object = new Config();
    }
    
    /**
     * @test
     */
    public function canBeInstantiatedIndividually()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Config',
            $this->object
        );
    }
    
    /**
     * @test
     */
    public function nullSettingReturnsFalse()
    {
        $this->assertFalse($this->object->set(null, null));
    }
    
    /**
     * @test
     */
    public function falseSettingReturnsFalse()
    {
        $this->assertFalse($this->object->set(false, null));
    }
    
    /**
     * @test
     */
    public function emptySettingReturnsFalse()
    {
        $this->assertFalse($this->object->set('', null));
    }
     
    /**
     * @test
     */
    public function validSettingReturnsTrue()
    {
        $this->assertTrue($this->object->set('setting', 'value'));
    }
    
    /**
     * @test
     */
    public function settingNullValueReturnsTrue()
    {
        $this->assertTrue($this->object->set('key', null));
    }
    
    /**
     * @test
     */
    public function settingFalseValueReturnsTrue()
    {
        $this->assertTrue($this->object->set('key', false));
    }
    
    /**
     * @test
     */
    public function settingEmptyValueReturnsTrue()
    {
        $this->assertTrue($this->object->set('key', ''));
    }
    
    /**
     * @test
     */
    public function gettingNonExistentSettingReturnsFalse()
    {
        $this->assertFalse($this->object->get('noexist'));
    }
    
    /**
     * @test
     */
    public function gettingNullSettingReturnsFalse()
    {
        $this->assertFalse($this->object->get(null));
    }
    
    /**
     * @test
     */
    public function gettingFalseSettingReturnsFalse()
    {
        $this->assertFalse($this->object->get(false));
    }
    
    /**
     * @test
     */
    public function gettingEmptySettingReturnsFalse()
    {
        $this->assertFalse($this->object->get(''));
    }
    
    /**
     * @test
     */
    public function gettingEmptyArraySettingReturnsFalse()
    {
        $this->assertFalse($this->object->get([]));
    }
    
    /**
     * @test
     */
    public function storingAndRetrievingSimpleStringSettingWorks()
    {
        $this->assertTrue($this->object->set($this->settingSimpleString, $this->value));
        $this->assertEquals($this->value, $this->object->get($this->settingSimpleString));
    }
    
    /**
     * @test
     */
    public function storingAndRetrievingArraySettingWorks()
    {
        $this->assertTrue($this->object->set($this->settingArray, $this->value));
        $this->assertEquals($this->value, $this->object->get($this->settingArray));
    }
    
    /**
     * @test
     */
    public function storingAndRetrievingSpecialStringSettingWorks()
    {
        $this->assertTrue($this->object->set($this->settingSpecialString, $this->value));
        $this->assertEquals($this->value, $this->object->get($this->settingSpecialString));
    }
    
    /**
     * @test
     */
    public function settingsTreeIsNoOverwrittenOnSpecialStringSetting()
    {
        $this->assertTrue($this->object->set('app.one.sub_two.key', $this->value));
        $this->assertTrue($this->object->set('app.two.sub_two.key', $this->value));
        $this->assertTrue($this->object->set('app.three.sub_three.key', $this->value));
        $this->assertEquals($this->value, $this->object->get('app.one.sub_two.key'));
    }
    
    /**
     * @test
     */
    public function settingsTreeIsOverwrittenOnRootKeySimpleStringSetting()
    {
        $this->assertTrue($this->object->set('app.one.sub_two.key', $this->value));
        $this->assertTrue($this->object->set('app.two.sub_two.key', $this->value));
        $this->assertTrue($this->object->set('app.three.sub_three.key', $this->value));
        $this->assertTrue($this->object->set('app', $this->value));
        $this->assertEquals($this->value, $this->object->get('app'));
    }
    
    /**
     * @test
     */
    public function settingSameKeyTwiceOverwritesTheFirst()
    {
        $this->assertTrue($this->object->set('foo', 'old value'));
        $this->assertTrue($this->object->set('foo', 'new value'));
        $this->assertEquals('new value', $this->object->get('foo'));
    }
    
    /**
     * @test
     */
    public function settingSameMultilevelKeyTwiceOverwritesTheFirst()
    {
        $this->assertTrue($this->object->set('foo.bar.baz', 'old value'));
        $this->assertTrue($this->object->set('foo.bar.baz', 'new value'));
        $this->assertEquals('new value', $this->object->get('foo.bar.baz'));
    }
    
    /**
     * @test
     */
    public function addReturnsTrue()
    {
        $this->assertTrue($this->object->add('add', 'dda'));
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
        $this->assertTrue($this->object->set('foo.bar.baz', 'old value'));
        $this->assertTrue($this->object->set('foo.bar.baz', 'new value'));
        $this->assertTrue($this->object->add('foo', $config));
        
        $this->assertEquals('value', $this->object->get('foo.level1.level2.level3.0'));
        $this->assertEquals('new value', $this->object->get('foo.bar.baz'));
    }
    
    /**
     * @test
     */
    public function loadReturnsFalseOnInvalidPath()
    {
        $this->assertFalse($this->object->load('foo', '/foo/bar'));
    }
    
    /**
     * @test
     */
    public function dummyConfigFileExists()
    {
        $this->assertTrue(is_readable(self::$pathProject . 'config/dev/foo.php'));
    }
    
    /**
     * @test
     * @depends dummyConfigFileExists
     */
    public function loadReturnsArrayOnValidPath()
    {
        $this->assertInternalType('array', $this->object->load('foo', self::$pathProject));
    }
    
    /**
     * @test
     * @depends loadReturnsArrayOnValidPath
     */
    public function addDataFromFileWorks()
    {
        $data = $this->object->load('foo', self::$pathProject);
        $this->assertTrue($this->object->add('foo', $data));
        $this->assertEquals('value1', $this->object->get('foo.options.setting1'));
    }
    /**
     * @test
     * @depends loadReturnsArrayOnValidPath
     */
    public function loadAppendsDataInsteadOfOverwriting()
    {
        $this->assertTrue($this->object->set('foo.bar.baz', 'new value'));
        $data = $this->object->load('foo', self::$pathProject);
        $this->object->add('foo', $data);
        $this->assertEquals('new value', $this->object->get('foo.bar.baz'));
    }
    
    /**
     * @test
     */
    public function setEnvReturnsTrue()
    {
        $this->assertTrue($this->object->setEnv('dev'));
    }
    
    /**
     * @test
     */
    public function setEnvDefaultsToDevOnInvalidValue()
    {
        $this->object->setEnv('noexist');
        $this->assertEquals('dev', $this->object->getEnv());
    }
    
    /**
     * @test
     */
    public function getEnvReturnsString()
    {
        $this->assertInternalType('string', $this->object->getEnv());
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
