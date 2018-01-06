<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Response;

final class ResponseInstanceTest extends TestCase
{
    private $object;
    
    public function setUp()
    {
        $this->object = new Response();
    }
    
    /**
     * @test
     */
    public function canBeInstantiatedIndividually()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Response',
            $this->object
        );
    }
    
    /**
     * @test
     */
    public function formatStatusHeaderTextReturnsFalseOnNullCode()
    {
        $this->assertFalse($this->object->formatStatusHeaderText(null));
    }
    
    /**
     * @test
     */
    public function formatStatusHeaderTextReturnsFalseOnEmptyCode()
    {
        $this->assertFalse($this->object->formatStatusHeaderText(''));
    }
    
    /**
     * @test
     */
    public function formatStatusHeaderTextReturnsFalseOnInvalidCode()
    {
        $this->assertFalse($this->object->formatStatusHeaderText(999));
    }
    
    /**
     * @test
     */
    public function formatStatusHeaderTextReturnsCorrectlyFormattedValueOnInvalidCode()
    {
        $this->assertEquals('HTTP/1.1 200 OK', $this->object->formatStatusHeaderText(200));
    }
    
    /**
     * @test
     */
    public function setStatusHeaderReturnsFalseOnEmptyCode()
    {
        $this->assertFalse($this->object->setStatusHeader(''));
    }
    
    /**
     * @test
     */
    public function setStatusHeaderReturnsFalseOnInvalidCode()
    {
        $this->assertFalse($this->object->setStatusHeader(999));
    }
}
