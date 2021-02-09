<?php declare(strict_types = 1);

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;

final class RequestTest extends TestCase
{

    /**
     * @test
     */
    public function canBeAccessedViaFramework(): void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Request',
            Fw::library('Request')
        );
    }

    /**
     * @test
     */
    public function getSchemaReturnsEmptyStringOnCli(): void
    {
        $this->assertEquals('', Fw::library('Request')->getSchema());
    }

    /**
     * @test
     */
    public function getRefererReturnsEmptyStringOnCli(): void
    {
        $this->assertEquals('', Fw::library('Request')->getReferer());
    }

    /**
     * @test
     */
    public function getHostReturnsString(): void
    {
        $this->assertIsString(Fw::library('Request')->getHost());
    }

    /**
     * @test
     */
    public function sanitizeRemovesBadChars(): void
    {
        $this->assertEquals(
            ['?&#39;&#34;?!~#^&*=[]:;||{}()x'],
            Fw::library('Request')->sanitize(
                ["?`'\"?!~#^&*=[]:;\||{}()\$\b\n\r\tx"]
            )
        );
    }

    /**
     * @test
     */
    public function sanitizeRemovesTags(): void
    {
        $this->assertEquals(
            ['script=alert(&#39;hacked!&#39;).html&key=value'],
            Fw::library('Request')->sanitize(
                ["script=<script>alert('hacked!')</script>.html&key=value"]
            )
        );
    }
}
