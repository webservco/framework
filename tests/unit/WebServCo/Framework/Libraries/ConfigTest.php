<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Config;
use WebServCo\Framework\Settings as S;

final class ConfigTest extends TestCase
{
    private static string $pathProject = '';

    private string $settingSimpleString = 'setting';

    /**
    * @var array<int,string>
    */
    private $settingArray = ['setting_array1', 'setting_array2', 'setting_array3'];

    private string $settingSpecialString = 'setting1.setting2.setting3';

    private string $value = 'value';

    public static function setUpBeforeClass() : void
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

    public static function tearDownAfterClass() : void
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

    public function setUp() : void
    {
        /**
         * Reset data to prevent phpunit hanging
         */
        Fw::library('Config')->set('app', null);
        Fw::library('Config')->set('foo', null);
    }

    /**
     * @test
     */
    public function canBeAccessedViaFramework() : void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Config',
            Fw::library('Config')
        );
    }

    /**
     * @test
     */
    public function nullSettingReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Config')->set(null, null));
    }

    /**
     * @test
     */
    public function falseSettingReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Config')->set(false, null));
    }

    /**
     * @test
     */
    public function emptySettingReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Config')->set('', null));
    }

    /**
     * @test
     */
    public function validSettingReturnsTrue() : void
    {
        $this->assertTrue(Fw::library('Config')->set('setting', 'value'));
    }

    /**
     * @test
     */
    public function settingNullValueReturnsTrue() : void
    {
        $this->assertTrue(Fw::library('Config')->set('key', null));
    }

    /**
     * @test
     */
    public function settingFalseValueReturnsTrue() : void
    {
        $this->assertTrue(Fw::library('Config')->set('key', false));
    }

    /**
     * @test
     */
    public function settingEmptyValueReturnsTrue() : void
    {
        $this->assertTrue(Fw::library('Config')->set('key', ''));
    }

    /**
     * @test
     * @depends validSettingReturnsTrue
     */
    public function frameworkAccessUsesSingleInstance() : void
    {
        $this->assertEquals('value', Fw::library('Config')->get('setting'));
    }

    /**
     * @test
     */
    public function gettingNonExistentSettingReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Config')->get('noexist'));
    }

    /**
     * @test
     */
    public function gettingNullSettingReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Config')->get(null));
    }

    /**
     * @test
     */
    public function gettingFalseSettingReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Config')->get(false));
    }

    /**
     * @test
     */
    public function gettingEmptySettingReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Config')->get(''));
    }

    /**
     * @test
     */
    public function gettingEmptyArraySettingReturnsFalse() : void
    {
        $this->assertFalse(Fw::library('Config')->get([]));
    }

    /**
     * @test
     */
    public function storingAndRetrievingSimpleStringSettingWorks() : void
    {
        $this->assertTrue(
            Fw::library('Config')->set(
                $this->settingSimpleString,
                $this->value
            )
        );
        $this->assertEquals(
            $this->value,
            Fw::library('Config')->get($this->settingSimpleString)
        );
    }

    /**
     * @test
     */
    public function storingAndRetrievingArraySettingWorks() : void
    {
        $this->assertTrue(
            Fw::library('Config')->set(
                $this->settingArray,
                $this->value
            )
        );
        $this->assertEquals(
            $this->value,
            Fw::library('Config')->get($this->settingArray)
        );
    }

    /**
     * @test
     */
    public function storingAndRetrievingSpecialStringSettingWorks() : void
    {
        $this->assertTrue(Fw::library('Config')->set($this->settingSpecialString, $this->value));
        $this->assertEquals($this->value, Fw::library('Config')->get($this->settingSpecialString));
    }

    /**
     * @test
     */
    public function settingsTreeIsNoOverwrittenOnSpecialStringSetting() : void
    {
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER),
                $this->value
            )
        );
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('app%1$stwo%1$ssub_two%1$skey', S::DIVIDER),
                $this->value
            )
        );
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('app%1$sthree%1$ssub_three%1$skey', S::DIVIDER),
                $this->value
            )
        );
        $this->assertEquals(
            $this->value,
            Fw::library('Config')->get(
                sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER)
            )
        );
    }

    /**
     * @test
     */
    public function settingsTreeIsOverwrittenOnRootKeySimpleStringSetting() : void
    {
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER),
                $this->value
            )
        );
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('app%1$stwo%1$ssub_two%1$skey', S::DIVIDER),
                $this->value
            )
        );
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('app%1$sthree%1$ssub_three%1$skey', S::DIVIDER),
                $this->value
            )
        );
        $this->assertTrue(
            Fw::library('Config')->set('app', $this->value)
        );
        $this->assertEquals(
            $this->value,
            Fw::library('Config')->get('app')
        );
    }

    /**
     * @test
     */
    public function settingSameKeyTwiceOverwritesTheFirst() : void
    {
        $this->assertTrue(Fw::library('Config')->set('foo', 'old value'));
        $this->assertTrue(Fw::library('Config')->set('foo', 'new value'));
        $this->assertEquals('new value', Fw::library('Config')->get('foo'));
    }

    /**
     * @test
     */
    public function settingSameMultilevelKeyTwiceOverwritesTheFirst() : void
    {
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'old value'
            )
        );
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value'
            )
        );
        $this->assertEquals(
            'new value',
            Fw::library('Config')->get(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER)
            )
        );
    }

    /**
     * @test
     */
    public function addReturnsTrue() : void
    {
        $this->assertTrue(Fw::library('Config')->add('add', 'dda'));
    }

    /**
     * @test
     */
    public function addAppendsDataInsteadOfOverwriting() : void
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
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'old value'
            )
        );
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value'
            )
        );
        $this->assertTrue(Fw::library('Config')->add('foo', $config));

        $this->assertEquals(
            'value',
            Fw::library('Config')->get(
                sprintf(
                    'foo%1$slevel1%1$slevel2%1$slevel3%1$s0',
                    S::DIVIDER
                )
            )
        );
        $this->assertEquals(
            'new value',
            Fw::library('Config')->get(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER)
            )
        );
    }

    /**
     * @test
     */
    public function loadReturnsEmptyArrayOnInvalidPath() : void
    {
        $this->assertEquals(
            [],
            Fw::library('Config')->load('foo', '/foo/bar')
        );
    }

    /**
     * @test
     */
    public function dummyConfigFileExists() : void
    {
        $this->assertTrue(
            is_readable(self::$pathProject . 'config/dev/foo.php')
        );
    }

    /**
     * @test
     * @depends dummyConfigFileExists
     */
    public function loadReturnsArrayOnValidPath() : void
    {
        $this->assertInternalType(
            'array',
            Fw::library('Config')->load('foo', self::$pathProject)
        );
    }

    /**
     * @test
     * @depends loadReturnsArrayOnValidPath
     */
    public function addDataFromFileWorks() : void
    {
        $data = Fw::library('Config')->load('foo', self::$pathProject);
        $this->assertTrue(Fw::library('Config')->add('foo', $data));
        $this->assertEquals(
            'value1',
            Fw::library('Config')->get(
                sprintf('foo%1$soptions%1$ssetting1', S::DIVIDER)
            )
        );
    }
    /**
     * @test
     * @depends loadReturnsArrayOnValidPath
     */
    public function loadAppendsDataInsteadOfOverwriting() : void
    {
        $this->assertTrue(
            Fw::library('Config')->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value'
            )
        );
        $data = Fw::library('Config')->load('foo', self::$pathProject);
        Fw::library('Config')->add('foo', $data);
        $this->assertEquals(
            'new value',
            Fw::library('Config')->get(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER)
            )
        );
    }

    /**
     * @test
     */
    public function setEnvReturnsTrue() : void
    {
        $this->assertTrue(Fw::library('Config')->setEnv('dev'));
    }

    /**
     * @test
     */
    public function setEnvDefaultsToDevOnInvalidValue() : void
    {
        Fw::library('Config')->setEnv('noexist');
        $this->assertEquals('dev', Fw::library('Config')->getEnv());
    }

    /**
     * @test
     */
    public function getEnvReturnsString() : void
    {
        $this->assertInternalType(
            'string',
            Fw::library('Config')->getEnv()
        );
    }

    /**
     * @test
     */
    public function getEnvDefaultsToDev() : void
    {
        $config = new \WebServCo\Framework\Libraries\Config;
        $this->assertEquals('dev', $config->getEnv());
    }
}
