<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework\Environment;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Values\Environment;

final class ValueTest extends TestCase
{
    /**
     * @test
     */
    public function constantEnvDevHasExpectedValue(): void
    {
        $this->assertEquals('development', Environment::DEVELOPMENT);
    }

    /**
     * @test
     */
    public function constantEnvTestHasExpectedValue(): void
    {
        $this->assertEquals('testing', Environment::TESTING);
    }

    /**
     * @test
     */
    public function constantEnvStagingHasExpectedValue(): void
    {
        $this->assertEquals('staging', Environment::STAGING);
    }

    /**
     * @test
     */
    public function constantEnvProdHasExpectedValue(): void
    {
        $this->assertEquals('production', Environment::PRODUCTION);
    }
}
