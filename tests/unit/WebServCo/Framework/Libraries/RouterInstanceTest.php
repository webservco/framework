<?php

declare(strict_types=1);

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Router;

final class RouterInstanceTest extends TestCase
{
    /**
     * Cfg.
     *
     * @var array<string,array<mixed>>
     */
    private array $cfg;

    private Router $object;

    public function setUp(): void
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
    public function canBeInstantiatedIndividually(): void
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Router', $this->object);
    }

    /**
     * @test
     */
    public function getRouteReturnsArrayOnEmptyData(): void
    {
        $route = $this->object->getRoute('', []);
        $this->assertInstanceOf('WebServCo\Framework\Objects\Route', $route);
    }

    /**
     * @test
     */
    public function getRouteReturnsArrayOnNullData(): void
    {
        $route = $this->object->getRoute('', []);
        $this->assertInstanceOf('WebServCo\Framework\Objects\Route', $route);
    }

    /**
     * @test
     */
    public function getRouteReturnsArrayOnValidData(): void
    {
        $route = $this->object->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertInstanceOf('WebServCo\Framework\Objects\Route', $route);
    }

    /**
     * @test
     */
    public function getRouteReturnsValidData(): void
    {
        $route = $this->object->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertInstanceOf('WebServCo\Framework\Objects\Route', $route);
        $this->assertEquals('foo', $route->class);
        $this->assertEquals('bar', $route->method);
        $this->assertEquals(['baz'], $route->arguments);
    }

    /**
     * @test
     */
    public function getRouteReturnsValidDataWithCustomRoutes(): void
    {
        $route = $this->object->getRoute('qwerty', $this->cfg['routes']);
        $this->assertInstanceOf('WebServCo\Framework\Objects\Route', $route);
        $this->assertEquals('Content', $route->class);
        $this->assertEquals('debugSomething', $route->method);
        $this->assertEquals(['foo', 'bar'], $route->arguments);
    }
}
