<?php declare(strict_types = 1);

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Environment;

final class EnvironmentTest extends TestCase
{

    /**
     * @test
     */
    public function constantEnvDevHasExpectedValue(): void
    {
        $this->assertEquals('dev', Environment::DEV);
    }

    /**
     * @test
     */
    public function constantEnvTestHasExpectedValue(): void
    {
        $this->assertEquals('test', Environment::TEST);
    }

    /**
     * @test
     */
    public function constantEnvProdHasExpectedValue(): void
    {
        $this->assertEquals('prod', Environment::PROD);
    }

    /**
     * @test
     */
    public function getOptionsReturnsExpectedValues(): void
    {
        $this->assertEquals(['dev', 'test', 'prod'], Environment::getOptions());
    }
}
