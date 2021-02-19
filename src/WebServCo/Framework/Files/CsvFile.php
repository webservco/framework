<?php

declare(strict_types=1);

namespace WebServCo\Framework\Files;

class CsvFile extends AbstractFile implements \WebServCo\Framework\Interfaces\FileInterface
{

    public const CONTENT_TYPE = 'text/csv';
}
