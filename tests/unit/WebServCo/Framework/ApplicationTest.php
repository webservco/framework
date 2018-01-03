<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Application as App;

final class ApplicationTest extends TestCase
{
    private $pathProject = '';
    private $pathWeb = '';
    
    public function setUp()
    {
        $pathProject = '/tmp/webservco/project/';
        $pathWeb = "{$pathProject}public/";
        if (!is_readable($pathWeb)) {
                mkdir($pathWeb, 0775, true);
                file_put_contents("{$pathProject}.env", 'dev');
        }
        $this->pathProject = $pathProject;
        $this->pathWeb = $pathWeb;
    }
    
    public function tearDown()
    {
        $pathBase = '/tmp/webservco/';
        $pathProject = "{$pathBase}project/";
        $pathWeb = "{$pathProject}public/";
        if (is_readable($pathWeb)) {
            rmdir($pathWeb);
            if (is_readable("{$pathProject}.env")) {
                unlink("{$pathProject}.env");
            }
            rmdir($pathProject);
            rmdir($pathBase);
        }
    }
    
    /**
    * @test
    */
    public function dummyProjectPathIsReadable()
    {
        $this->assertTrue(is_readable($this->pathProject));
    }
    
    /**
    * @test
    */
    public function dummyWebPathIsReadable()
    {
        $this->assertTrue(is_readable($this->pathWeb));
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
            new App($this->pathWeb, $this->pathProject)
        );
    }
    
    /**
    * @test
    * @depends instantiationWithValidParametersWorks
    */
    public function setEnvironmentValueReturnsTrue()
    {
        $app = new App($this->pathWeb, $this->pathProject);
        $this->assertTrue($app->setEnvironmentValue());
    }
    
    /**
    * @test
    *
    * @depends instantiationWithValidParametersWorks
    */
    public function startReturnsTrue()
    {
        $app = new App($this->pathWeb, $this->pathProject);
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
        $app = new App($this->pathWeb, $this->pathProject);
        $this->assertTrue($app->stop());
    }
}
