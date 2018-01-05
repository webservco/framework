<?php
namespace Tests\Framework\Libraries;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Libraries\Log;

final class LogInstanceTest extends TestCase
{
    private $object;
    
    public function setUp()
    {
        $this->object = new Log();
    }
    
    /**
     * @test
     */
    public function canBeInstantiatedIndividually()
    {
        $this->assertInstanceOf(
            'WebServCo\Framework\Libraries\Log',
            $this->object
        );
    }
}
