<?php

namespace Tests\Framework;

use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Framework as Fw;
use WebServCo\Framework\ErrorHandler;

final class ErrorHandlerTest extends TestCase
{
    /**
     * @test
     */
    public function setReturnsTrue()
    {
        $this->assertTrue(ErrorHandler::set());
    }
    
    /**
     * @test
     */
    public function restoreReturnsTrue()
    {
        $this->assertTrue(ErrorHandler::restore());
    }
    
    /**
     * @test
     * @expectedException \ErrorException
     */
    public function throwsErrorExceptionWorks()
    {
        ErrorHandler::throwErrorException(256, 'Custom error message', 'foo/bar.php', 13);
    }
}
