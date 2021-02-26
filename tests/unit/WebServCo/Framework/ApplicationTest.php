<?php

declare(strict_types=1);

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Application as App;

final class ApplicationTest extends TestCase
{

    private static string $pathProject = '';
    private static string $pathWeb = '';

    /**
    * @test
    */
    public function dummyProjectPathIsReadable(): void
    {
        $this->assertTrue(\is_readable(self::$pathProject));
    }

    /**
    * @test
    */
    public function dummyWebPathIsReadable(): void
    {
        $this->assertTrue(\is_readable(self::$pathWeb));
    }

    /**
    * @test
    */
    public function instantiationWithEmptyParametersThrowsException(): void
    {
        $this->expectException(\WebServCo\Framework\Exceptions\ApplicationException::class);
        new App('', '');
    }

    /**
    * @test
    */
    public function instantiationWithDummyParametersThrowsException(): void
    {
        $this->expectException(\WebServCo\Framework\Exceptions\ApplicationException::class);
        new App('foo', 'bar');
    }

    /**
    * @test
    */
    public function instantiationInvalidParametersThrowsException(): void
    {
        $this->expectException(\WebServCo\Framework\Exceptions\ApplicationException::class);
        new App('/tmp', '/tmp');
    }

    /**
    * @test
    * @depends dummyProjectPathIsReadable
    * @depends dummyWebPathIsReadable
    */
    public function instantiationWithValidParametersWorks(): void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Application',
            new App(self::$pathWeb, self::$pathProject),
        );
    }

    /**
    * @test
    * @depends instantiationWithValidParametersWorks
    */
    public function setEnvironmentValueReturnsTrue(): void
    {
        $app = new App(self::$pathWeb, self::$pathProject);
        $this->assertTrue($app->setEnvironmentValue());
    }

    /**
     * @test
     */
    public function shutdownMethodIsPublic(): void
    {
        $app = new App(self::$pathWeb, self::$pathProject);
        $reflection = new \ReflectionMethod($app, 'shutdown');
        $this->assertTrue($reflection->isPublic());
    }

    public static function setUpBeforeClass(): void
    {
        $pathProject = '/tmp/webservco/project/';
        $pathWeb = "{$pathProject}public/";

        if (!\is_readable($pathWeb)) {
                \mkdir($pathWeb, 0775, true);
                \touch("{$pathWeb}index.php");
                \file_put_contents("{$pathProject}.env", 'dev');
        }
        self::$pathProject = $pathProject;
        self::$pathWeb = $pathWeb;
    }

    public static function tearDownAfterClass(): void
    {
        $pathBase = '/tmp/webservco/';
        $it = new \RecursiveDirectoryIterator($pathBase, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach ($files as $item) {
            if ($item->isDir()) {
                \rmdir($item->getRealPath());
            } else {
                \unlink($item->getRealPath());
            }
        }
        \rmdir($pathBase);
    }
}
