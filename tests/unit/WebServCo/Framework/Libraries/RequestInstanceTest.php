<?php declare(strict_types = 1);

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Request;

final class RequestInstanceTest extends TestCase
{
    /**
    * @var array<string,array<int,string>>
    */
    private array $cfg;

    /**
    * @var array<string,string>
    */
    private array $post;

    private Request $object;

    private Request $objectPost;

    public function setUp(): void
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
    public function canBeInstantiatedIndividually(): void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Request',
            $this->object
        );
    }

    /**
     * @test
     */
    public function getSchemaReturnsEmptyStringOnCli(): void
    {
        $this->assertEquals('', $this->object->getSchema());
    }

    /**
     * @test
     */
    public function getRefererReturnsEmptyStringOnCli(): void
    {
        $this->assertEquals('', $this->object->getReferer());
    }

    /**
     * @test
     */
    public function getHostReturnsString(): void
    {
        $this->assertIsString($this->object->getHost());
    }

    /**
     * @test
     */
    public function sanitizeRemovesBadChars(): void
    {
        $this->assertEquals(
            ['foo' => '?&#39;&#34;?!~#^&*=[]:;||{}()x'],
            $this->object->sanitize(['foo' => "?`'\"?!~#^&*=[]:;\||{}()\$\b\n\r\tx"])
        );
    }

    /**
     * @test
     */
    public function sanitizeRemovesTags(): void
    {
        $this->assertEquals(
            ['foo' => 'script=alert(&#39;hacked!&#39;).html&key=value'],
            $this->object->sanitize(
                ['foo' => "script=<script>alert('hacked!')</script>.html&key=value"]
            )
        );
    }

    /**
     * @test
     */
    public function postRequestIsParsedCorrectly(): void
    {
        $this->assertEquals(
            'value',
            $this->objectPost->data('key')
        );
    }

    /**
     * @test
     */
    public function postRequestTagsNotDisabledInValues(): void
    {
        $this->assertEquals(
            '<script>hello</script>',
            $this->objectPost->data('script')
        );
    }

    /**
     * @test
     */
    public function postRequestTagsDisabledInKeys(): void
    {
        $this->assertFalse($this->objectPost->data('<h1>invalid</h1>'));
        $this->assertEquals('<tag>tag</tag>', $this->objectPost->data('invalid'));
    }
}
