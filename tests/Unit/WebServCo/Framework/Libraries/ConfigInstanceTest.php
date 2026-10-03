<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework\Libraries;

use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use WebServCo\Framework\Libraries\Config;
use WebServCo\Framework\Settings;

use function file_put_contents;
use function is_readable;
use function mkdir;
use function rmdir;
use function sprintf;
use function unlink;

final class ConfigInstanceTest extends TestCase
{
    private Config $object;

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
        $this->object = new Config();
    }

    #[Test]
    public function canBeInstantiatedIndividually(): void
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Config', $this->object);
    }

    #[Test]
    public function nullSettingReturnsFalse(): void
    {
        $this->assertFalse($this->object->set(null, null));
    }

    #[Test]
    public function falseSettingReturnsFalse(): void
    {
        $this->assertFalse($this->object->set(false, null));
    }

    #[Test]
    public function emptySettingReturnsFalse(): void
    {
        $this->assertFalse($this->object->set('', null));
    }

    #[Test]
    public function validSettingReturnsTrue(): void
    {
        $this->assertTrue($this->object->set('setting', 'value'));
    }

    #[Test]
    public function settingNullValueReturnsTrue(): void
    {
        $this->assertTrue($this->object->set('key', null));
    }

    #[Test]
    public function settingFalseValueReturnsTrue(): void
    {
        $this->assertTrue($this->object->set('key', false));
    }

    #[Test]
    public function settingEmptyValueReturnsTrue(): void
    {
        $this->assertTrue($this->object->set('key', ''));
    }

    #[Test]
    public function gettingNonExistentSettingReturnsNull(): void
    {
        $this->assertNull($this->object->get('noexist'));
    }

    #[Test]
    public function gettingNullSettingReturnsNull(): void
    {
        $this->assertNull($this->object->get(null));
    }

    #[Test]
    public function gettingFalseSettingReturnsNull(): void
    {
        $this->assertNull($this->object->get(false));
    }

    #[Test]
    public function gettingEmptySettingReturnsNull(): void
    {
        $this->assertNull($this->object->get(''));
    }

    #[Test]
    public function gettingEmptyArraySettingReturnsNull(): void
    {
        $this->assertNull($this->object->get([]));
    }

    #[Test]
    public function storingAndRetrievingSimpleStringSettingWorks(): void
    {
        $this->assertTrue($this->object->set($this->settingSimpleString, $this->value));
        $this->assertEquals($this->value, $this->object->get($this->settingSimpleString));
    }

    #[Test]
    public function storingAndRetrievingArraySettingWorks(): void
    {
        $this->assertTrue($this->object->set($this->settingArray, $this->value));
        $this->assertEquals($this->value, $this->object->get($this->settingArray));
    }

    #[Test]
    public function storingAndRetrievingSpecialStringSettingWorks(): void
    {
        $this->assertTrue($this->object->set($this->settingSpecialString, $this->value));
        $this->assertEquals($this->value, $this->object->get($this->settingSpecialString));
    }

    #[Test]
    public function settingsTreeIsNoOverwrittenOnSpecialStringSetting(): void
    {
        $this->assertTrue(
            $this->object->set(sprintf('app%1$sone%1$ssub_two%1$skey', Settings::DIVIDER), $this->value),
        );
        $this->assertTrue(
            $this->object->set(sprintf('app%1$stwo%1$ssub_two%1$skey', Settings::DIVIDER), $this->value),
        );
        $this->assertTrue(
            $this->object->set(sprintf('app%1$sthree%1$ssub_three%1$skey', Settings::DIVIDER), $this->value),
        );
        $this->assertEquals(
            $this->value,
            $this->object->get(sprintf('app%1$sone%1$ssub_two%1$skey', Settings::DIVIDER)),
        );
    }

    #[Test]
    public function settingsTreeIsOverwrittenOnRootKeySimpleStringSetting(): void
    {
        $this->assertTrue(
            $this->object->set(sprintf('app%1$sone%1$ssub_two%1$skey', Settings::DIVIDER), $this->value),
        );
        $this->assertTrue(
            $this->object->set(sprintf('app%1$stwo%1$ssub_two%1$skey', Settings::DIVIDER), $this->value),
        );
        $this->assertTrue(
            $this->object->set(sprintf('app%1$sthree%1$ssub_three%1$skey', Settings::DIVIDER), $this->value),
        );
        $this->assertTrue($this->object->set('app', $this->value));
        $this->assertEquals($this->value, $this->object->get('app'));
    }

    #[Test]
    public function settingSameKeyTwiceOverwritesTheFirst(): void
    {
        $this->assertTrue($this->object->set('foo', 'old value'));
        $this->assertTrue($this->object->set('foo', 'new value'));
        $this->assertEquals('new value', $this->object->get('foo'));
    }

    #[Test]
    public function settingSameMultilevelKeyTwiceOverwritesTheFirst(): void
    {
        $this->assertTrue($this->object->set(sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER), 'old value'));
        $this->assertTrue($this->object->set(sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER), 'new value'));
        $this->assertEquals('new value', $this->object->get(sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER)));
    }

    #[Test]
    public function addReturnsTrue(): void
    {
        $this->assertTrue($this->object->add('add', 'dda'));
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
        $this->assertTrue($this->object->set(sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER), 'old value'));
        $this->assertTrue($this->object->set(sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER), 'new value'));
        $this->assertTrue($this->object->add('foo', $config));

        $this->assertEquals(
            'value',
            $this->object->get(sprintf('foo%1$slevel1%1$slevel2%1$slevel3%1$s0', Settings::DIVIDER)),
        );
        $this->assertEquals('new value', $this->object->get(sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER)));
    }

    #[Test]
    public function loadReturnsEmptyArrayInvalidPath(): void
    {
        $this->assertEquals([], $this->object->load('foo', '/foo/bar'));
    }

    #[Test]
    public function dummyConfigFileExists(): void
    {
        $this->assertTrue(is_readable(self::$pathProject . 'config/foo.php'));
    }

    #[Depends('dummyConfigFileExists')]
    #[Test]
    public function loadReturnsArrayOnValidPath(): void
    {
        $this->assertIsArray($this->object->load('foo', self::$pathProject));
    }

    #[Depends('loadReturnsArrayOnValidPath')]
    #[Test]
    public function addDataFromFileWorks(): void
    {
        $data = $this->object->load('foo', self::$pathProject);
        $this->assertTrue($this->object->add('foo', $data));
        $this->assertEquals('value1', $this->object->get(sprintf('foo%1$soptions%1$ssetting1', Settings::DIVIDER)));
    }

    #[Depends('loadReturnsArrayOnValidPath')]
    #[Test]
    public function loadAppendsDataInsteadOfOverwriting(): void
    {
        $this->assertTrue($this->object->set(sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER), 'new value'));
        $data = $this->object->load('foo', self::$pathProject);
        $this->object->add('foo', $data);
        $this->assertEquals('new value', $this->object->get(sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER)));
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
