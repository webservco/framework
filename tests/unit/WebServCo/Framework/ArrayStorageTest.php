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
            'key' => 'value',
            'foo' => [
                'bar' => [
                    'baz' => [
                        'foobarbaz',
                    ],
                ],
            ],
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
            'key' => 'value',
            'foo' => [
                'bar' => [],
            ],
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
            'key' => 'value',
            'foo' => [],
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
            'key' => 'value',
            'foo' => [
                'bar' => [
                    'baz' => null,
                ],
            ],
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
            'key' => 'value',
            'foo' => [
                'bar' => null,
            ],
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
            'key' => 'value',
            'foo' => null,
        ];
        $this->assertEquals($expected, ArrayStorage::set(
            $this->originalArray,
            'foo',
            null,
        ));
    }
}
