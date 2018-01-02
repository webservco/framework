<?php

namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework;
use WebServCo\Framework\Libraries\Config;

final class ConfigTest extends TestCase
{
    //can be instantiated
    //can be accessed via framework
    //uses only one instance (set a value in a test, get it in another)
    
    /**
     * @test
     */
    public function canBeInstantiatedIndividually()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Config',
            new Config()
        );
    }
    
    /**
     * @test
     */
    public function canBeAccessedViaFramework()
    {
        $this->assertInstanceOf('WebServCo\Framework\Libraries\Log', Framework::log());
    }
}
