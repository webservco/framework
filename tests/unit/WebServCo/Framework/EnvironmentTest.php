<?php
namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Environment;

final class EnvironmentTest extends TestCase
{
    /**
     * @test
     */
    public function constantEnvDevHasExpectedValue()
    {
        $this->assertEquals('dev', Environment::DEV);
    }

    /**
     * @test
     */
    public function constantEnvTestHasExpectedValue()
    {
        $this->assertEquals('test', Environment::TEST);
    }

    /**
     * @test
     */
    public function constantEnvProdHasExpectedValue()
    {
        $this->assertEquals('prod', Environment::PROD);
    }

    /**
     * @test
     */
    public function getOptionsReturnsExpectedValues()
    {
        $this->assertEquals(['dev','test','prod'], Environment::getOptions());
    }
}
