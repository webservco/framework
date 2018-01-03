<?php

namespace Tests;

use PHPUnit\Framework\TestCase;

final class Test extends TestCase
{
    /**
     * @test
     */
    public function dummyPassingTest()
    {
        $this->assertTrue(true);
    }
    
    /**
     * @test
     */
    public function blank()
    {
        $this->markTestIncomplete('TODO');
    }
}
