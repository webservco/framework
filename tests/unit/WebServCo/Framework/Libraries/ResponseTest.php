<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Response;

final class ResponseTest extends TestCase
{
    /**
     * @test
     */
    public function canBeAccessedViaFramework()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Response', Fw::response());
    }
    
    /**
     * @test
     */
    public function formatStatusHeaderTextReturnsFalseOnNullCode()
    {
        $this->assertFalse(Fw::response()->formatStatusHeaderText(null));
    }
    
    /**
     * @test
     */
    public function formatStatusHeaderTextReturnsFalseOnEmptyCode()
    {
        $this->assertFalse(Fw::response()->formatStatusHeaderText(''));
    }
    
    /**
     * @test
     */
    public function formatStatusHeaderTextReturnsFalseOnInvalidCode()
    {
        $this->assertFalse(Fw::response()->formatStatusHeaderText(999));
    }
    
    /**
     * @test
     */
    public function formatStatusHeaderTextReturnsCorrectlyFormattedValueOnInvalidCode()
    {
        $this->assertEquals('HTTP/1.1 200 OK', Fw::response()->formatStatusHeaderText(200));
    }
    
    /**
     * @test
     */
    public function setStatusHeaderReturnsFalseOnEmptyCode()
    {
        $this->assertFalse(Fw::response()->setStatusHeader(''));
    }
    
    /**
     * @test
     */
    public function setStatusHeaderReturnsFalseOnInvalidCode()
    {
        $this->assertFalse(Fw::response()->setStatusHeader(999));
    }
}
