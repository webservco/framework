<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework;

use ErrorException;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WebServCo\Framework\ErrorHandler;

final class ErrorHandlerTest extends TestCase
{
    #[Test]
    public function setReturnsTrue(): void
    {
        $this->assertTrue(ErrorHandler::set());
    }

    #[Test]
    public function restoreReturnsTrue(): void
    {
        $this->assertTrue(ErrorHandler::restore());
    }

    #[Test]
    public function throwsErrorExceptionWorks(): void
    {
        $this->expectException(ErrorException::class);
        ErrorHandler::throwErrorException(256, 'Custom error message', 'foo/bar.php', 13);
    }
}
