<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework\Libraries;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Helpers\RouterLibraryHelper;

final class RouterTest extends TestCase
{
    /**
     * Cfg
     *
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

    #[Test]
    public function canBeAccessedViaFramework(): void
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Router',
            RouterLibraryHelper::library(),
        );
    }

    #[Test]
    public function getRouteReturnsArrayOnValidData(): void
    {
        $route = RouterLibraryHelper::library()->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertInstanceOf('WebServCo\Framework\Objects\Route', $route);
    }

    #[Test]
    public function getRouteReturnsValidData(): void
    {
        $route = RouterLibraryHelper::library()->getRoute('foo/bar/baz', $this->cfg['routes']);
        $this->assertInstanceOf('WebServCo\Framework\Objects\Route', $route);
        $this->assertEquals('foo', $route->class);
        $this->assertEquals('bar', $route->method);
        $this->assertEquals(['baz'], $route->arguments);
    }

    #[Test]
    public function getRouteReturnsValidDataWithCustomRoutes(): void
    {
        $route = RouterLibraryHelper::library()->getRoute('qwerty', $this->cfg['routes']);
        $this->assertInstanceOf('WebServCo\Framework\Objects\Route', $route);
        $this->assertEquals('Content', $route->class);
        $this->assertEquals('debugSomething', $route->method);
        $this->assertEquals(['foo', 'bar'], $route->arguments);
    }
}
