<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

use WebServCo\Framework\Interfaces\FileInterface;

final class PdfFile extends AbstractFile implements FileInterface
{
    public const string CONTENT_TYPE = 'application/pdf';
}
