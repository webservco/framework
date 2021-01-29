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
    public function setReturnsTrue(): void
    {
        $this->assertTrue(ErrorHandler::set());
    }

    /**
     * @test
     */
    public function restoreReturnsTrue(): void
    {
        $this->assertTrue(ErrorHandler::restore());
    }

    /**
     * @test
     */
    public function throwsErrorExceptionWorks(): void
    {
        $this->expectException(\ErrorException::class);
        ErrorHandler::throwErrorException(256, 'Custom error message', 'foo/bar.php', 13);
    }
}
