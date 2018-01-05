<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Router;

final class RouterTest extends TestCase
{
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
    }
    
    /**
     * @test
     */
    public function canBeAccessedViaFramework()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Router', Fw::router());
    }
    
    /**
     * @test
     */
    public function getRouteReturnsArrayOnEmptyData()
    {
        $route = Fw::router()->getRoute('', []);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
    }
    
    /**
     * @test
     */
    public function getRouteReturnsArrayOnNullData()
    {
        $route = Fw::router()->getRoute(null, []);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
    }
    
    /**
     * @test
     */
    public function getRouteReturnsArrayOnValidData()
    {
        $route = Fw::router()->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
    }
    
    /**
     * @test
     */
    public function getRouteReturnsValidData()
    {
        $route = Fw::router()->getRoute('foo/bar/baz', $this->cfg['routes']);
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
        $route = Fw::router()->getRoute('qwerty', $this->cfg['routes']);
        $this->assertInternalType('array', $route);
        $this->assertEquals(3, count($route));
        $this->assertEquals('Content', $route[0]);
        $this->assertEquals('debugSomething', $route[1]);
        $this->assertEquals(['foo','bar'], $route[2]);
    }
}
