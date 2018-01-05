<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Request;

final class RequestInstanceTest extends TestCase
{
    private $object;
    
    public function setUp()
    {
        $cfg = ['suffixes' => ['.htm','.html'],];
        $this->object = new Request($cfg, $_SERVER);
    }
    
    /**
     * @test
     */
    public function canBeInstantiatedIndividually()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Request',
            $this->object
        );
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnNull()
    {
        $this->assertInternalType('array', $this->object->split(null));
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnEmptyValue()
    {
        $this->assertInternalType('array', $this->object->split(''));
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnValidValue()
    {
        $this->assertInternalType('array', $this->object->split('foo/bar'));
    }
    
    /**
     * @test
     */
    public function getSchemaReturnsNullOnCli()
    {
        $this->assertNull($this->object->getSchema());
    }
    
    /**
     * @test
     */
    public function getRefererReturnsNullOnCli()
    {
        $this->assertNull($this->object->getReferer());
    }
    
    /**
     * @test
     */
    public function getHostReturnsString()
    {
        $this->assertInternalType('string', $this->object->getHost());
    }
}
