<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework\Libraries;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use WebServCo\Framework\Helpers\ConfigLibraryHelper;
use WebServCo\Framework\Settings as S;

use function file_put_contents;
use function is_readable;
use function mkdir;
use function rmdir;
use function sprintf;
use function unlink;

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
        ConfigLibraryHelper::library()->set('app', null);
        ConfigLibraryHelper::library()->set('foo', null);
    }

    #[Test]
    public function canBeAccessedViaFramework(): void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Config',
            ConfigLibraryHelper::library(),
        );
    }

    #[Test]
    public function nullSettingReturnsFalse(): void
    {
        $this->assertFalse(ConfigLibraryHelper::library()->set(null, null));
    }

    #[Test]
    public function falseSettingReturnsFalse(): void
    {
        $this->assertFalse(ConfigLibraryHelper::library()->set(false, null));
    }

    #[Test]
    public function emptySettingReturnsFalse(): void
    {
        $this->assertFalse(ConfigLibraryHelper::library()->set('', null));
    }

    #[Test]
    public function validSettingReturnsTrue(): void
    {
        $this->assertTrue(ConfigLibraryHelper::library()->set('setting', 'value'));
    }

    #[Test]
    public function settingNullValueReturnsTrue(): void
    {
        $this->assertTrue(ConfigLibraryHelper::library()->set('key', null));
    }

    #[Test]
    public function settingFalseValueReturnsTrue(): void
    {
        $this->assertTrue(ConfigLibraryHelper::library()->set('key', false));
    }

    #[Test]
    public function settingEmptyValueReturnsTrue(): void
    {
        $this->assertTrue(ConfigLibraryHelper::library()->set('key', ''));
    }

    #[Depends('validSettingReturnsTrue')]
    #[Test]
    public function frameworkAccessUsesSingleInstance(): void
    {
        $this->assertEquals('value', ConfigLibraryHelper::library()->get('setting'));
    }

    #[Test]
    public function gettingNonExistentSettingReturnsNull(): void
    {
        $this->assertNull(ConfigLibraryHelper::library()->get('noexist'));
    }

    #[Test]
    public function gettingNullSettingReturnsNull(): void
    {
        $this->assertNull(ConfigLibraryHelper::library()->get(null));
    }

    #[Test]
    public function gettingFalseSettingReturnsNull(): void
    {
        $this->assertNull(ConfigLibraryHelper::library()->get(false));
    }

    #[Test]
    public function gettingEmptySettingReturnsNull(): void
    {
        $this->assertNull(ConfigLibraryHelper::library()->get(''));
    }

    #[Test]
    public function gettingEmptyArraySettingReturnsNull(): void
    {
        $this->assertNull(ConfigLibraryHelper::library()->get([]));
    }

    #[Test]
    public function storingAndRetrievingSimpleStringSettingWorks(): void
    {
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                $this->settingSimpleString,
                $this->value,
            ),
        );
        $this->assertEquals(
            $this->value,
            ConfigLibraryHelper::library()->get($this->settingSimpleString),
        );
    }

    #[Test]
    public function storingAndRetrievingArraySettingWorks(): void
    {
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                $this->settingArray,
                $this->value,
            ),
        );
        $this->assertEquals(
            $this->value,
            ConfigLibraryHelper::library()->get($this->settingArray),
        );
    }

    #[Test]
    public function storingAndRetrievingSpecialStringSettingWorks(): void
    {
        $this->assertTrue(
            ConfigLibraryHelper::library()->set($this->settingSpecialString, $this->value),
        );
        $this->assertEquals(
            $this->value,
            ConfigLibraryHelper::library()->get($this->settingSpecialString),
        );
    }

    #[Test]
    public function settingsTreeIsNoOverwrittenOnSpecialStringSetting(): void
    {
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('app%1$stwo%1$ssub_two%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('app%1$sthree%1$ssub_three%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertEquals(
            $this->value,
            ConfigLibraryHelper::library()->get(
                sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER),
            ),
        );
    }

    #[Test]
    public function settingsTreeIsOverwrittenOnRootKeySimpleStringSetting(): void
    {
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('app%1$sone%1$ssub_two%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('app%1$stwo%1$ssub_two%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('app%1$sthree%1$ssub_three%1$skey', S::DIVIDER),
                $this->value,
            ),
        );
        $this->assertTrue(
            ConfigLibraryHelper::library()->set('app', $this->value),
        );
        $this->assertEquals(
            $this->value,
            ConfigLibraryHelper::library()->get('app'),
        );
    }

    #[Test]
    public function settingSameKeyTwiceOverwritesTheFirst(): void
    {
        $this->assertTrue(ConfigLibraryHelper::library()->set('foo', 'old value'));
        $this->assertTrue(ConfigLibraryHelper::library()->set('foo', 'new value'));
        $this->assertEquals('new value', ConfigLibraryHelper::library()->get('foo'));
    }

    #[Test]
    public function settingSameMultilevelKeyTwiceOverwritesTheFirst(): void
    {
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'old value',
            ),
        );
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value',
            ),
        );
        $this->assertEquals(
            'new value',
            ConfigLibraryHelper::library()->get(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
            ),
        );
    }

    #[Test]
    public function addReturnsTrue(): void
    {
        $this->assertTrue(ConfigLibraryHelper::library()->add('add', 'dda'));
    }

    #[Test]
    public function addAppendsDataInsteadOfOverwriting(): void
    {

        $config = [
            'level1' => [
                'level2' => [
                    'level3' => ['value'],
                ],
            ],
            'options' => [
                'setting1' => 'value1',
                'setting2' => 'value2',
                'setting3' => 'value3',
            ],
        ];
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'old value',
            ),
        );
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value',
            ),
        );
        $this->assertTrue(ConfigLibraryHelper::library()->add('foo', $config));

        $this->assertEquals(
            'value',
            ConfigLibraryHelper::library()->get(
                sprintf(
                    'foo%1$slevel1%1$slevel2%1$slevel3%1$s0',
                    S::DIVIDER,
                ),
            ),
        );
        $this->assertEquals(
            'new value',
            ConfigLibraryHelper::library()->get(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
            ),
        );
    }

    #[Test]
    public function loadReturnsEmptyArrayOnInvalidPath(): void
    {
        $this->assertEquals(
            [],
            ConfigLibraryHelper::library()->load('foo', '/foo/bar'),
        );
    }

    #[Test]
    public function dummyConfigFileExists(): void
    {
        $this->assertTrue(
            is_readable(self::$pathProject . 'config/foo.php'),
        );
    }

    #[Depends('dummyConfigFileExists')]
    #[Test]
    public function loadReturnsArrayOnValidPath(): void
    {
        $this->assertIsArray(
            ConfigLibraryHelper::library()->load('foo', self::$pathProject),
        );
    }

    #[Depends('loadReturnsArrayOnValidPath')]
    #[Test]
    public function addDataFromFileWorks(): void
    {
        $data = ConfigLibraryHelper::library()->load('foo', self::$pathProject);
        $this->assertTrue(ConfigLibraryHelper::library()->add('foo', $data));
        $this->assertEquals(
            'value1',
            ConfigLibraryHelper::library()->get(
                sprintf('foo%1$soptions%1$ssetting1', S::DIVIDER),
            ),
        );
    }

    #[Depends('loadReturnsArrayOnValidPath')]
    #[Test]
    public function loadAppendsDataInsteadOfOverwriting(): void
    {
        $this->assertTrue(
            ConfigLibraryHelper::library()->set(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
                'new value',
            ),
        );
        $data = ConfigLibraryHelper::library()->load('foo', self::$pathProject);
        ConfigLibraryHelper::library()->add('foo', $data);
        $this->assertEquals(
            'new value',
            ConfigLibraryHelper::library()->get(
                sprintf('foo%1$sbar%1$sbaz', S::DIVIDER),
            ),
        );
    }

    public static function setUpBeforeClass(): void
    {
        $pathProject = '/tmp/webservco/project/';
        $pathConfig = "{$pathProject}config/";
        if (!is_readable($pathConfig)) {
                mkdir($pathConfig, 0775, true);
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
                file_put_contents("{$pathConfig}foo.php", $data);
        }
        self::$pathProject = $pathProject;
    }

    public static function tearDownAfterClass(): void
    {
        $pathBase = '/tmp/webservco/';
        $it = new RecursiveDirectoryIterator($pathBase, RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($files as $item) {
            if ($item->isDir()) {
                rmdir($item->getRealPath());
            } else {
                unlink($item->getRealPath());
            }
        }
        rmdir($pathBase);
    }
}
