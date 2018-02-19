<?php
namespace Tests\Framework;

use PHPUnit\Framework\TestCase;

use WebServCo\Framework\Settings as S;
use WebServCo\Framework\ArrayStorage;

final class ArrayStorageTest extends TestCase
{
    private $originalArray;
    
    public function setUp()
    {
        $this->originalArray = [
            'key' => 'value',
            'foo' => [
                'bar' => [
                    'baz' => [
                        'foobarbaz'
                    ],
                ]
            ],
        ];
    }
    
    /**
     * @test
     * @expectedException \WebServCo\Framework\Exceptions\ArrayStorageException
     */
    public function unsetWithNonExistingTripleSettingThrowsException()
    {
        $setting = sprintf('foo%1$snotBar%1$sbaz', S::DIVIDER);
        ArrayStorage::remove(
            $this->originalArray,
            $setting
        );
    }
    
    /**
     * @test
     * @expectedException \WebServCo\Framework\Exceptions\ArrayStorageException
     */
    public function unsetWithNonExistingDoubleSettingThrowsException()
    {
        $setting = sprintf('foo%1$snotBar', S::DIVIDER);
        ArrayStorage::remove(
            $this->originalArray,
            $setting
        );
    }
    
    /**
     * @test
     * @expectedException \WebServCo\Framework\Exceptions\ArrayStorageException
     */
    public function unsetWithNonExistingSimpleSettingThrowsException()
    {
        ArrayStorage::remove(
            $this->originalArray,
            'noexist'
        );
    }
    
    /**
     * @test
     */
    public function unsetWorksWithTripleSetting()
    {
        $setting = sprintf('foo%1$sbar%1$sbaz', S::DIVIDER);
        $expected = [
            'key' => 'value',
            'foo' => [
                'bar' => [],
            ],
        ];
        $this->assertEquals($expected, ArrayStorage::remove(
            $this->originalArray,
            $setting
        ));
    }
    
    /**
     * @test
     */
    public function unsetWorksWithDoubleSetting()
    {
        $setting = sprintf('foo%1$sbar', S::DIVIDER);
        $expected = [
            'key' => 'value',
            'foo' => [],
        ];
        $this->assertEquals($expected, ArrayStorage::remove(
            $this->originalArray,
            $setting
        ));
    }
    
    /**
     * @test
     */
    public function unsetWorksWithSimpleSetting()
    {
        $expected = [
            'key' => 'value',
        ];
        $this->assertEquals($expected, ArrayStorage::remove(
            $this->originalArray,
            'foo'
        ));
    }
    
    /**
     * @test
     */
    public function setEmptyWorksWithTripleSetting()
    {
        $setting = sprintf('foo%1$sbar%1$sbaz', S::DIVIDER);
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
            null
        ));
    }
    
    /**
     * @test
     */
    public function setEmptyWorksWithDoubleSetting()
    {
        $setting = sprintf('foo%1$sbar', S::DIVIDER);
        $expected = [
            'key' => 'value',
            'foo' => [
                'bar' => null,
            ],
        ];
        $this->assertEquals($expected, ArrayStorage::set(
            $this->originalArray,
            $setting,
            null
        ));
    }
    
    /**
     * @test
     */
    public function setEmptyWorksWithSimpleSetting()
    {
        $expected = [
            'key' => 'value',
            'foo' => null,
        ];
        $this->assertEquals($expected, ArrayStorage::set(
            $this->originalArray,
            'foo',
            null
        ));
    }
}
