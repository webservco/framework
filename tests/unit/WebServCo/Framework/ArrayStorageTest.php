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
     * @expectedException \ErrorException
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
     * @expectedException \ErrorException
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
     * @expectedException \ErrorException
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
    public function clearWorksWithTripleSetting()
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
        $this->assertEquals($expected, ArrayStorage::clear(
            $this->originalArray,
            $setting
        ));
    }
    
    /**
     * @test
     */
    public function clearWorksWithDoubleSetting()
    {
        $setting = sprintf('foo%1$sbar', S::DIVIDER);
        $expected = [
            'key' => 'value',
            'foo' => [
                'bar' => null,
            ],
        ];
        $this->assertEquals($expected, ArrayStorage::clear(
            $this->originalArray,
            $setting
        ));
    }
    
    /**
     * @test
     */
    public function clearWorksWithSimpleSetting()
    {
        $expected = [
            'key' => 'value',
            'foo' => null,
        ];
        $this->assertEquals($expected, ArrayStorage::clear(
            $this->originalArray,
            'foo'
        ));
    }
}
