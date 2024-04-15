<?php

declare(strict_types=1);

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\ArrayStorage;
use WebServCo\Framework\Settings;

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

    /**
     * @test
     */
    public function unsetWithNonExistingTripleSettingThrowsException(): void
    {
        $this->expectException(\WebServCo\Framework\Exceptions\ArrayStorageException::class);
        $setting = \sprintf('foo%1$snotBar%1$sbaz', Settings::DIVIDER);
        ArrayStorage::remove($this->originalArray, $setting);
    }

    /**
     * @test
     */
    public function unsetWithNonExistingDoubleSettingThrowsException(): void
    {
        $this->expectException(\WebServCo\Framework\Exceptions\ArrayStorageException::class);
        $setting = \sprintf('foo%1$snotBar', Settings::DIVIDER);
        ArrayStorage::remove($this->originalArray, $setting);
    }

    /**
     * @test
     */
    public function unsetWithNonExistingSimpleSettingThrowsException(): void
    {
        $this->expectException(\WebServCo\Framework\Exceptions\ArrayStorageException::class);
        ArrayStorage::remove($this->originalArray, 'noexist');
    }

    /**
     * @test
     */
    public function unsetWorksWithTripleSetting(): void
    {
        $setting = \sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER);
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

    /**
     * @test
     */
    public function unsetWorksWithDoubleSetting(): void
    {
        $setting = \sprintf('foo%1$sbar', Settings::DIVIDER);
        $expected = [
            'foo' => [],
            'key' => 'value',
        ];
        $this->assertEquals($expected, ArrayStorage::remove(
            $this->originalArray,
            $setting,
        ));
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function setEmptyWorksWithTripleSetting(): void
    {
        $setting = \sprintf('foo%1$sbar%1$sbaz', Settings::DIVIDER);
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

    /**
     * @test
     */
    public function setEmptyWorksWithDoubleSetting(): void
    {
        $setting = \sprintf('foo%1$sbar', Settings::DIVIDER);
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

    /**
     * @test
     */
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
