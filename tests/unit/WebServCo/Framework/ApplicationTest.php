<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Application as App;

final class ApplicationTest extends TestCase
{
    protected $path_project = '';
    protected $path_public = '';
    
    public function setUp() {
        $fw_path = Fw::getPath();
        $this->path_project = "{$fw_path}tests/assets/project/";
        $this->path_public = "{$fw_path}tests/assets/project/public/";
    }
     
     /**
     * @test
     */
     public function instantiationWithNullParametersThrowsException()
     {
         $this->expectException(\Exception::class);
         
         new App(null,null);
     }
     
     /**
     * @test
     */
     public function instantiationWithEmptyParametersThrowsException()
     {
         $this->expectException(\Exception::class);
         
         new App('','');
     }
     
     /**
     * @test
     */
     public function instantiationWithDummyParametersThrowsException()
     {
         $this->expectException(\Exception::class);
         
         new App('foo','bar');
     }
     
     /**
     * @test
     */
     public function instantiationWithValidParametersWorks()
     {
         $this->assertInstanceOf('WebServCo\Framework\Application', new App($this->path_project,$this->path_public));
     }
}
