<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Application as App;

final class ApplicationTest extends TestCase
{
    private static $pathProject = '';
    private static $pathWeb = '';
    
    public static function setUpBeforeClass()
    {
        $pathProject = '/tmp/webservco/project/';
        $pathWeb = "{$pathProject}public/";
        if (!is_readable($pathWeb)) {
                mkdir($pathWeb, 0775, true);
                touch("{$pathWeb}index.php");
                file_put_contents("{$pathProject}.env", 'dev');
        }
        self::$pathProject = $pathProject;
        self::$pathWeb = $pathWeb;
    }

    public static function tearDownAfterClass()
    {
        $pathBase = '/tmp/webservco/';
        $pathProject = "{$pathBase}project/";
        $pathWeb = "{$pathProject}public/";
        if (is_readable($pathWeb)) {
            if (is_readable(("{$pathWeb}index.php"))) {
                    unlink("{$pathWeb}index.php");
            }
            rmdir($pathWeb);
            if (is_readable("{$pathProject}.env")) {
                unlink("{$pathProject}.env");
            }
            if (is_readable("{$pathProject}foo.php")) {
                unlink("{$pathProject}foo.php");
            }
            rmdir($pathProject);
            rmdir($pathBase);
        }
    }
    
    public function setUp()
    {
    }
    
    public function tearDown()
    {
    }
    
    /**
    * @test
    */
    public function dummyProjectPathIsReadable()
    {
        $this->assertTrue(is_readable(self::$pathProject));
    }
    
    /**
    * @test
    */
    public function dummyWebPathIsReadable()
    {
        $this->assertTrue(is_readable(self::$pathWeb));
    }

    /**
    * @test
    * @expectedException \ErrorException
    */
    public function instantiationWithNullParametersThrowsException()
    {
        new App(null, null);
    }
     
    /**
    * @test
    * @expectedException \ErrorException
    */
    public function instantiationWithEmptyParametersThrowsException()
    {
        new App('', '');
    }
     
    /**
    * @test
    * @expectedException \ErrorException
    */
    public function instantiationWithDummyParametersThrowsException()
    {
        new App('foo', 'bar');
    }
     
    /**
    * @test
    * @expectedException \ErrorException
    */
    public function instantiationInvalidParametersThrowsException()
    {
        new App('/tmp', '/tmp');
    }
    
    /**
    * @test
    * @depends dummyProjectPathIsReadable
    * @depends dummyWebPathIsReadable
    */
    public function instantiationWithValidParametersWorks()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Application',
            new App(self::$pathWeb, self::$pathProject)
        );
    }
    
    /**
    * @test
    * @depends instantiationWithValidParametersWorks
    */
    public function setEnvironmentValueReturnsTrue()
    {
        $app = new App(self::$pathWeb, self::$pathProject);
        $this->assertTrue($app->setEnvironmentValue());
    }
    
    /**
    * @test
    *
    * @depends instantiationWithValidParametersWorks
    */
    public function startReturnsTrue()
    {
        $app = new App(self::$pathWeb, self::$pathProject);
        $this->assertTrue($app->start());
    }
    
    /**
    * @test
    *
    * @depends instantiationWithValidParametersWorks
    * @depends startReturnsTrue
    */
    public function stopReturnsTrue()
    {
        $app = new App(self::$pathWeb, self::$pathProject);
        $this->assertTrue($app->stop());
    }
}
