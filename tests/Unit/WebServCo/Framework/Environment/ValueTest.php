<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework\Environment;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Values\Environment;

final class ValueTest extends TestCase
{
    #[Test]
    public function constantEnvDevHasExpectedValue(): void
    {
        $this->assertEquals('development', Environment::DEVELOPMENT);
    }

    #[Test]
    public function constantEnvTestHasExpectedValue(): void
    {
        $this->assertEquals('testing', Environment::TESTING);
    }

    #[Test]
    public function constantEnvStagingHasExpectedValue(): void
    {
        $this->assertEquals('staging', Environment::STAGING);
    }

    #[Test]
    public function constantEnvProdHasExpectedValue(): void
    {
        $this->assertEquals('production', Environment::PRODUCTION);
    }
}
