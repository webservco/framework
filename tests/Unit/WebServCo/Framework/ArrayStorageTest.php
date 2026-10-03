<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WebServCo\Framework\ArrayStorage;
use WebServCo\Framework\Exceptions\ArrayStorageException;
use WebServCo\Framework\Settings;

use function sprintf;

final class ArrayStorageTest extends TestCase
{
    /**
    * Original array.
    *
    * @var array<array<mixed>|string>
    */
    private array $originalArray;

    public function setUp(): void
    {
        $this->originalArray = [
            'foo' => [
                'bar' => [
                    'baz' => [
                        'foobarbaz',
                    ],
                ],
            ],
            'key' => 'value',
        ];
    }

    #[Test]
    public function unsetWithNonExistingTripleSettingThrowsException(): void
    {
        $this->expectException(ArrayStorageException::class);
        $setting = sprintf('foo%1$snotBar%1$sbaz', Settings::DIVIDER);
        ArrayStorage::remove($this->originalArray, $setting);
    }

    #[Test]
    public function unsetWithNonExistingDoubleSettingThrowsException(): void
    {
        $this->expectException(ArrayStorageException::class);
        $setting = sprintf('foo%1$snotBar', Settings::DIVIDER);
        ArrayStorage::remove($this->originalArray, $setting);
    }

    #[Test]
    public function unsetWithNonExistingSimpleSettingThrowsException(): void
    {
        $this->expectException(ArrayStorageException::class);
        ArrayStorage::remove($this->originalArray, 'noexist');
    }

    #[Test]
    public function unsetWorksWithTripleSetting(): void
    {
        $setting = sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER);
        $expected = [
            'foo' => [
                'bar' => [],
            ],
            'key' => 'value',
        ];
        $this->assertEquals($expected, ArrayStorage::remove(
            $this->originalArray,
            $setting,
        ));
    }

    #[Test]
    public function unsetWorksWithDoubleSetting(): void
    {
        $setting = sprintf('foo%1$sbar', Settings::DIVIDER);
        $expected = [
            'foo' => [],
            'key' => 'value',
        ];
        $this->assertEquals($expected, ArrayStorage::remove(
            $this->originalArray,
            $setting,
        ));
    }

    #[Test]
    public function unsetWorksWithSimpleSetting(): void
    {
        $expected = [
            'key' => 'value',
        ];
        $this->assertEquals($expected, ArrayStorage::remove(
            $this->originalArray,
            'foo',
        ));
    }

    #[Test]
    public function setEmptyWorksWithTripleSetting(): void
    {
        $setting = sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER);
        $expected = [
            'foo' => [
                'bar' => [
                    'baz' => null,
                ],
            ],
            'key' => 'value',
        ];
        $this->assertEquals($expected, ArrayStorage::set(
            $this->originalArray,
            $setting,
            null,
        ));
    }

    #[Test]
    public function setEmptyWorksWithDoubleSetting(): void
    {
        $setting = sprintf('foo%1$sbar', Settings::DIVIDER);
        $expected = [
            'foo' => [
                'bar' => null,
            ],
            'key' => 'value',
        ];
        $this->assertEquals($expected, ArrayStorage::set(
            $this->originalArray,
            $setting,
            null,
        ));
    }

    #[Test]
    public function setEmptyWorksWithSimpleSetting(): void
    {
        $expected = [
            'foo' => null,
            'key' => 'value',
        ];
        $this->assertEquals($expected, ArrayStorage::set(
            $this->originalArray,
            'foo',
            null,
        ));
    }
}
