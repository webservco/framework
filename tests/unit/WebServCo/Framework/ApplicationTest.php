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
     
    /**
    * @test
    */
    public function instantiationWithNullParametersThrowsException()
    {
        $this->expectException('\ErrorException');
         
        new App(null, null);
    }
     
    /**
    * @test
    */
    public function instantiationWithEmptyParametersThrowsException()
    {
        $this->expectException('\ErrorException');
         
        new App('', '');
    }
     
    /**
    * @test
    */
    public function instantiationWithDummyParametersThrowsException()
    {
        $this->expectException('\ErrorException');
         
        new App('foo', 'bar');
    }
     
    /**
    * @test
    */
    public function instantiationInvalidParametersThrowsException()
    {
        $this->expectException('\ErrorException');
         
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
