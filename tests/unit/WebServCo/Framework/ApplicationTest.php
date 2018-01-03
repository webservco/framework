<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use org\bovigo\vfs\vfsStream;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Application as App;

final class ApplicationTest extends TestCase
{
    private $filesystem;
    
    protected $pathProject = '';
    protected $pathWeb = '';
    
    public function setUp()
    {
        $this->filesystem = vfsStream::setup();
        $pathProject = Fw::getPath() . 'tests/assets/project/';
        vfsStream::copyFromFileSystem($pathProject, $this->filesystem);
        $this->pathProject = $this->filesystem->url() . '/';
        $this->pathWeb = $this->filesystem->url() . '/public/';
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
