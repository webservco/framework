<?php

declare(strict_types=1);

namespace Tests\Unit\WebServCo\Framework\Exceptions;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use WebServCo\Framework\Exceptions\UploadException;
use WebServCo\Framework\Files\Upload\Codes;

final class UploadExceptionTest extends TestCase
{
    #[Test]
    public function worksWithoutI18nLibrary(): void
    {
        $exception = new UploadException(Codes::NO_FILE);

        $this->assertSame('No file was uploaded.', $exception->getMessage());
        $this->assertSame(Codes::NO_FILE, $exception->getCode());
    }
}
