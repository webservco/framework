<?php

declare(strict_types=1);

namespace Tests\Framework\Environment;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Environment\Value;

final class ValueTest extends TestCase
{
    /**
     * @test
     */
    public function constantEnvDevHasExpectedValue(): void
    {
        $this->assertEquals('development', Value::DEVELOPMENT);
    }

    /**
     * @test
     */
    public function constantEnvTestHasExpectedValue(): void
    {
        $this->assertEquals('testing', Value::TESTING);
    }

    /**
     * @test
     */
    public function constantEnvStagingHasExpectedValue(): void
    {
        $this->assertEquals('staging', Value::STAGING);
    }

    /**
     * @test
     */
    public function constantEnvProdHasExpectedValue(): void
    {
        $this->assertEquals('production', Value::PRODUCTION);
    }
}
