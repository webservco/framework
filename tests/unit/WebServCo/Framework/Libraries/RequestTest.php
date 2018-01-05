<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Request;

final class RequestTest extends TestCase
{
    /**
     * @test
     */
    public function canBeAccessedViaFramework()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Request', Fw::request());
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnNull()
    {
        $this->assertInternalType('array', Fw::request()->split(null));
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnEmptyValue()
    {
        $this->assertInternalType('array', Fw::request()->split(''));
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnValidValue()
    {
        $this->assertInternalType('array', Fw::request()->split('foo/bar'));
    }
    
    /**
     * @test
     */
    public function getSchemaReturnsNullOnCli()
    {
        $this->assertNull(Fw::request()->getSchema());
    }
    
    /**
     * @test
     */
    public function getRefererReturnsNullOnCli()
    {
        $this->assertNull(Fw::request()->getReferer());
    }
    
    /**
     * @test
     */
    public function getHostReturnsString()
    {
        $this->assertInternalType('string', Fw::request()->getHost());
    }
}
