<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Router;

final class RouterInstanceTest extends TestCase
{
    private $object;
    private $cfg;
    
    public function setUp()
    {
        $this->cfg = [
            'default_route' => ['Content', 'home', ['foo', 'bar']],
            'routes' => [
                'blog/({any})/({num})' => 'Blog/article/$2',
                'qwerty' => 'Content/debugSomething/foo/bar',
            ],
        ];
        $this->object = new Router($this->cfg);
    }
    
    /**
     * @test
     */
    public function canBeInstantiatedIndividually()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Router',
            $this->object
        );
    }
    
    /**
     * @test
     */
    public function getRouteReturnsArrayOnEmptyData()
    {
        $route = $this->object->getRoute('', []);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
    }
    
    /**
     * @test
     */
    public function getRouteReturnsArrayOnNullData()
    {
        $route = $this->object->getRoute(null, []);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
    }
    
    /**
     * @test
     */
    public function getRouteReturnsArrayOnValidData()
    {
        $route = $this->object->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
    }
    
    /**
     * @test
     */
    public function getRouteReturnsValidData()
    {
        $route = $this->object->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
        $this->assertEquals('foo', $route[0]);
        $this->assertEquals('bar', $route[1]);
        $this->assertEquals(['baz'], $route[2]);
    }
    
    /**
     * @test
     */
    public function getRouteReturnsValidDataWithCustomRoutes()
    {
        $route = $this->object->getRoute('qwerty', $this->cfg['routes']);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
        $this->assertEquals('Content', $route[0]);
        $this->assertEquals('debugSomething', $route[1]);
        $this->assertEquals(['foo','bar'], $route[2]);
    }
}
