<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Request;

final class RequestInstanceTest extends TestCase
{
    private $cfg;
    private $post;
    private $object;
    private $objectPost;
    
    public function setUp()
    {
        $this->cfg = ['suffixes' => ['.htm','.html'],];
        $this->post = [
            'key' => 'value',
            'script' => '<script>hello</script>',
            '<h1>invalid</h1>' => '<tag>tag</tag>',
        ];
        $this->object = new Request($this->cfg, $_SERVER, $this->post);
        
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $this->objectPost = new Request($this->cfg, $_SERVER, $this->post);
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
    
    /**
     * @test
     */
    public function sanitizeRemovesBadChars()
    {
        $this->assertEquals(
            '?&#39;&#34;?!~#^&*=[]:;||{}()x',
            $this->object->sanitize("?`'\"?!~#^&*=[]:;\||{}()\$\b\n\r\tx")
        );
    }
    
    /**
     * @test
     */
    public function sanitizeRemovesTags()
    {
        $this->assertEquals(
            'script=alert(&#39;hacked!&#39;).html&key=value',
            $this->object->sanitize(
                "script=<script>alert('hacked!')</script>.html&key=value"
            )
        );
    }
    
    /**
     * @test
     */
    public function postRequestIsParsedCorrectly()
    {
        $this->assertEquals(
            'value',
            $this->objectPost->data('key')
        );
    }
    
    /**
     * @test
     */
    public function postRequestTagsNotDisabledInValues()
    {
        $this->assertEquals(
            '<script>hello</script>',
            $this->objectPost->data('script')
        );
    }
    
    /**
     * @test
     */
    public function postRequestTagsDisabledInKeys()
    {
        $this->assertFalse($this->objectPost->data('<h1>invalid</h1>'));
        $this->assertEquals('<tag>tag</tag>', $this->objectPost->data('invalid'));
    }
}
