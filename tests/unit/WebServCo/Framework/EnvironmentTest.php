<?php
namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\Environment as Env;

final class EnvironmentTest extends TestCase
{
    /**
     * @test
     */
    public function constantEnvDevHasExpectedValue()
    {
        $this->assertEquals('dev', Env::ENV_DEV);
    }
    
    /**
     * @test
     */
    public function constantEnvTestHasExpectedValue()
    {
        $this->assertEquals('test', Env::ENV_TEST);
    }
    
    /**
     * @test
     */
    public function constantEnvProdHasExpectedValue()
    {
        $this->assertEquals('prod', Env::ENV_PROD);
    }
    
    /**
     * @test
     */
    public function getOptionsReturnsExpectedValues()
    {
        $this->assertEquals(['dev','test','prod'], Env::getOptions());
    }
}
