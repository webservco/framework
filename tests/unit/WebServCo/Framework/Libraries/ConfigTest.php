<?php

declare(strict_types=1);

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Settings as S;

final class ConfigTest extends TestCase
{

    private string $settingSimpleString = 'setting';

    /**
    * Setting array.
    *
    * @var array<int,string>
    */
    private array $settingArray = ['setting_array1', 'setting_array2', 'setting_array3'];

    private string $settingSpecialString = 'setting1.setting2.setting3';

    private string $value = 'value';

    private static string $pathProject = '';

    public function setUp(): void
    {
        /**
         * Reset data to prevent phpunit hanging
         */
        \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('app', null);
        \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('foo', null);
    }

    /**
     * @test
     */
    public function canBeAccessedViaFramework(): void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Config',
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library(),
        );
    }

    /**
     * @test
     */
    public function nullSettingReturnsFalse(): void
    {
        $this->assertFalse(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(null, null));
    }

    /**
     * @test
     */
    public function falseSettingReturnsFalse(): void
    {
        $this->assertFalse(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(false, null));
    }

    /**
     * @test
     */
    public function emptySettingReturnsFalse(): void
    {
        $this->assertFalse(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('', null));
    }

    /**
     * @test
     */
    public function validSettingReturnsTrue(): void
    {
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('setting', 'value'));
    }

    /**
     * @test
     */
    public function settingNullValueReturnsTrue(): void
    {
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('key', null));
    }

    /**
     * @test
     */
    public function settingFalseValueReturnsTrue(): void
    {
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('key', false));
    }

    /**
     * @test
     */
    public function settingEmptyValueReturnsTrue(): void
    {
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('key', ''));
    }

    /**
     * @test
     * @depends validSettingReturnsTrue
     */
    public function frameworkAccessUsesSingleInstance(): void
    {
        $this->assertEquals('value', \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get('setting'));
    }

    /**
     * @test
     */
    public function gettingNonExistentSettingReturnsNull(): void
    {
        $this->assertNull(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get('noexist'));
    }

    /**
     * @test
     */
    public function gettingNullSettingReturnsNull(): void
    {
        $this->assertNull(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(null));
    }

    /**
     * @test
     */
    public function gettingFalseSettingReturnsNull(): void
    {
        $this->assertNull(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(false));
    }

    /**
     * @test
     */
    public function gettingEmptySettingReturnsNull(): void
    {
        $this->assertNull(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(''));
    }

    /**
     * @test
     */
    public function gettingEmptyArraySettingReturnsNull(): void
    {
        $this->assertNull(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get([]));
    }

    /**
     * @test
     */
    public function storingAndRetrievingSimpleStringSettingWorks(): void
    {
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                $this->settingSimpleString,
                $this->value,
            ),
        );
        $this->assertEquals(
            $this->value,
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get($this->settingSimpleString),
        );
    }

    /**
     * @test
     */
    public function storingAndRetrievingArraySettingWorks(): void
    {
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                $this->settingArray,
                $this->value,
            ),
        );
        $this->assertEquals(
            $this->value,
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get($this->settingArray),
        );
    }

    /**
     * @test
     */
    public function storingAndRetrievingSpecialStringSettingWorks(): void
    {
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set($this->settingSpecialString, $this->value),
        );
        $this->assertEquals(
            $this->value,
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get($this->settingSpecialString),
        );
    }

    /**
     * @test
     */
    public function settingsTreeIsNoOverwrittenOnSpecialStringSetting(): void
    {
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('app%1$stwo%1$ssub_two%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('app%1$sthree%1$ssub_three%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertEquals(
            $this->value,
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(
                \sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER),
            ),
        );
    }

    /**
     * @test
     */
    public function settingsTreeIsOverwrittenOnRootKeySimpleStringSetting(): void
    {
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('app%1$stwo%1$ssub_two%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('app%1$sthree%1$ssub_three%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('app', $this->value),
        );
        $this->assertEquals(
            $this->value,
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get('app'),
        );
    }

    /**
     * @test
     */
    public function settingSameKeyTwiceOverwritesTheFirst(): void
    {
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('foo', 'old value'));
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set('foo', 'new value'));
        $this->assertEquals('new value', \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get('foo'));
    }

    /**
     * @test
     */
    public function settingSameMultilevelKeyTwiceOverwritesTheFirst(): void
    {
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'old value',
            ),
        );
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value',
            ),
        );
        $this->assertEquals(
            'new value',
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(
                \sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
            ),
        );
    }

    /**
     * @test
     */
    public function addReturnsTrue(): void
    {
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->add('add', 'dda'));
    }

    /**
     * @test
     */
    public function addAppendsDataInsteadOfOverwriting(): void
    {

        $config = [
            'options' => [
                'setting1' => 'value1',
                'setting2' => 'value2',
                'setting3' => 'value3',
            ],
            'level1' => [
                'level2' => [
                    'level3' => ['value'],
                ],
            ],
        ];
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'old value',
            ),
        );
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value',
            ),
        );
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->add('foo', $config));

        $this->assertEquals(
            'value',
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(
                \sprintf(
                    'foo%1$slevel1%1$slevel2%1$slevel3%1$s0',
                    S::DIVIDER,
                ),
            ),
        );
        $this->assertEquals(
            'new value',
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(
                \sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
            ),
        );
    }

    /**
     * @test
     */
    public function loadReturnsEmptyArrayOnInvalidPath(): void
    {
        $this->assertEquals(
            [],
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->load('foo', '/foo/bar'),
        );
    }

    /**
     * @test
     */
    public function dummyConfigFileExists(): void
    {
        $this->assertTrue(
            \is_readable(self::$pathProject . 'config/development/foo.php'),
        );
    }

    /**
     * @test
     * @depends dummyConfigFileExists
     */
    public function loadReturnsArrayOnValidPath(): void
    {
        $this->assertIsArray(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->load('foo', self::$pathProject),
        );
    }

    /**
     * @test
     * @depends loadReturnsArrayOnValidPath
     */
    public function addDataFromFileWorks(): void
    {
        $data = \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->load('foo', self::$pathProject);
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->add('foo', $data));
        $this->assertEquals(
            'value1',
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(
                \sprintf('foo%1$soptions%1$ssetting1', S::DIVIDER),
            ),
        );
    }

    /**
     * @test
     * @depends loadReturnsArrayOnValidPath
     */
    public function loadAppendsDataInsteadOfOverwriting(): void
    {
        $this->assertTrue(
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->set(
                \sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value',
            ),
        );
        $data = \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->load('foo', self::$pathProject);
        \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->add('foo', $data);
        $this->assertEquals(
            'new value',
            \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->get(
                \sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
            ),
        );
    }

    /**
     * @test
     */
    public function setEnvReturnsTrue(): void
    {
        $this->assertTrue(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->setEnv('development'));
    }

    /**
     * @test
     */
    public function setEnvThrowsExceptionOnInvalidValue(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        \WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->setEnv('noexist');
    }

    /**
     * @test
     */
    public function getEnvReturnsString(): void
    {
        $this->assertIsString(\WebServCo\Framework\Helpers\ConfigLibraryHelper::library()->getEnv());
    }

    /**
     * @test
     */
    public function getEnvWhenNotSetThrowsException(): void
    {
        $this->expectException(\WebServCo\Framework\Exceptions\ApplicationException::class);
        $config = new \WebServCo\Framework\Libraries\Config();
        $config->getEnv();
    }

    public static function setUpBeforeClass(): void
    {
        $pathProject = '/tmp/webservco/project/';
        $pathConfig = "{$pathProject}config/development/";
        if (!\is_readable($pathConfig)) {
                \mkdir($pathConfig, 0775, true);
                $data = "<?php
                return [
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
                \file_put_contents("{$pathConfig}foo.php", $data);
        }
        self::$pathProject = $pathProject;
    }

    public static function tearDownAfterClass(): void
    {
        $pathBase = '/tmp/webservco/';
        $it = new \RecursiveDirectoryIterator($pathBase, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $item) {
            if ($item->isDir()) {
                \rmdir($item->getRealPath());
            } else {
                \unlink($item->getRealPath());
            }
        }
        \rmdir($pathBase);
    }
}
