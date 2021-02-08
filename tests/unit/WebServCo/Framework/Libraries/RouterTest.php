<?php declare(strict_types = 1);

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Libraries\Router;

final class RouterTest extends TestCase
{
    /**
    * @var array<string,array<mixed>>
    */
    private array $cfg;

    public function setUp(): void
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
    public function canBeAccessedViaFramework(): void
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Router', Fw::library('Router'));
    }

    /**
     * @test
     */
    public function getRouteReturnsArrayOnValidData(): void
    {
        $route = Fw::library('Router')->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertIsArray($route);
        $this->assertEquals(3, count($route));
    }

    /**
     * @test
     */
    public function getRouteReturnsValidData(): void
    {
        $route = Fw::library('Router')->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertIsArray($route);
        $this->assertEquals(3, count($route));
        $this->assertEquals('foo', $route[0]);
        $this->assertEquals('bar', $route[1]);
        $this->assertEquals(['baz'], $route[2]);
    }

    /**
     * @test
     */
    public function getRouteReturnsValidDataWithCustomRoutes(): void
    {
        $route = Fw::library('Router')->getRoute('qwerty', $this->cfg['routes']);
        $this->assertIsArray($route);
        $this->assertEquals(3, count($route));
        $this->assertEquals('Content', $route[0]);
        $this->assertEquals('debugSomething', $route[1]);
        $this->assertEquals(['foo','bar'], $route[2]);
    }
}
