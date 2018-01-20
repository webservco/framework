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
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Request', Fw::getLibrary('Request', [$_SERVER, $_POST]));
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnNull()
    {
        $this->assertInternalType('array', Fw::getLibrary('Request', [$_SERVER, $_POST])->split(null));
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnEmptyValue()
    {
        $this->assertInternalType('array', Fw::getLibrary('Request', [$_SERVER, $_POST])->split(''));
    }
    
    /**
     * @test
     */
    public function splitReturnsArrayOnValidValue()
    {
        $this->assertInternalType('array', Fw::getLibrary('Request', [$_SERVER, $_POST])->split('foo/bar'));
    }
    
    /**
     * @test
     */
    public function getSchemaReturnsNullOnCli()
    {
        $this->assertNull(Fw::getLibrary('Request', [$_SERVER, $_POST])->getSchema());
    }
    
    /**
     * @test
     */
    public function getRefererReturnsNullOnCli()
    {
        $this->assertNull(Fw::getLibrary('Request', [$_SERVER, $_POST])->getReferer());
    }
    
    /**
     * @test
     */
    public function getHostReturnsString()
    {
        $this->assertInternalType('string', Fw::getLibrary('Request', [$_SERVER, $_POST])->getHost());
    }
    
    /**
     * @test
     */
    public function sanitizeRemovesBadChars()
    {
        $this->assertEquals(
            '?&#39;&#34;?!~#^&*=[]:;||{}()x',
            Fw::getLibrary('Request', [$_SERVER, $_POST])->sanitize("?`'\"?!~#^&*=[]:;\||{}()\$\b\n\r\tx")
        );
    }
    
    /**
     * @test
     */
    public function sanitizeRemovesTags()
    {
        $this->assertEquals(
            'script=alert(&#39;hacked!&#39;).html&key=value',
            Fw::getLibrary('Request', [$_SERVER, $_POST])->sanitize(
                "script=<script>alert('hacked!')</script>.html&key=value"
            )
        );
    }
}
