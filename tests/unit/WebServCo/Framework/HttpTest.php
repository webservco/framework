<?php
namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Http;

final class HttpTest extends TestCase
{
    /**
     * @test
     */
    public function getMethodsReturnsArray()
    {
        $this->assertInternalType('array', Http::getMethods());
    }
    
    /**
     * @test
     */
    public function getStatusCodeMessageReturnsFalseOnEmptyValue()
    {
        $this->assertFalse(Http::getStatusCodeMessage(''));
    }
    
    /**
     * @test
     */
    public function getStatusCodeMessageReturnsFalseOnNullValue()
    {
        $this->assertFalse(Http::getStatusCodeMessage(null));
    }
    
    /**
     * @test
     */
    public function getStatusCodeMessageReturnsFalseOnInvalidValue()
    {
        $this->assertFalse(Http::getStatusCodeMessage(999));
    }
    
    /**
     * @test
     */
    public function getStatusCodeMessageReturnsCorrectValueOnValidCode()
    {
        $this->assertEquals('OK', Http::getStatusCodeMessage(200));
    }
}
