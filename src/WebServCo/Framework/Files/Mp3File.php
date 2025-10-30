<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

class Mp3File extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{
    public const string CONTENT_TYPE = 'audio/mpeg';
}
