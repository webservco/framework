<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Helpers\RequestLibraryHelper;

final class RequestTest extends TestCase
{
    /**
     * @test
     */
    public function canBeAccessedViaFramework(): void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Request',
            RequestLibraryHelper::library(),
        );
    }

    /**
     * @test
     */
    public function getSchemaReturnsEmptyStringOnCli(): void
    {
        $this->assertEquals('', RequestLibraryHelper::library()->getSchema());
    }

    /**
     * @test
     */
    public function getRefererReturnsEmptyStringOnCli(): void
    {
        $this->assertEquals('', RequestLibraryHelper::library()->getReferer());
    }

    /**
     * @test
     */
    public function getHostReturnsString(): void
    {
        $this->assertIsString(RequestLibraryHelper::library()->getHost());
    }

    /**
     * @test
     */
    public function sanitizeRemovesBadChars(): void
    {
        $this->assertEquals(
            ['test' => '?&#39;&#34;?!~#^&*=[]:;||{}()x'],
            RequestLibraryHelper::library()->sanitize(
                ['test' => "?`'\"?!~#^&*=[]:;\||{}()\$\b\n\r\tx"],
            ),
        );
    }

    /**
     * @test
     */
    public function sanitizeRemovesTags(): void
    {
        $this->assertEquals(
            ['test' => 'script=alert(&#39;hacked!&#39;).html&key=value'],
            RequestLibraryHelper::library()->sanitize(
                ['test' => "script=<script>alert('hacked!')</script>.html&key=value"],
            ),
        );
    }
}
