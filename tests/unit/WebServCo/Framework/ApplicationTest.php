<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Application as App;

final class ApplicationTest extends TestCase
{
    protected $pathProject = '';
    protected $pathWeb = '';
    
    public function setUp()
    {
        $pathProject = Fw::getPath() . 'tests/assets/project/';
        $this->pathProject = $pathProject;
        $this->pathWeb = "{$pathProject}public/";
    }
    
    public function test()
    {
        $this->assertEquals('XXX', $this->pathProject);
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
