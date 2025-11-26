<?php

declare(strict_types=1);

namespace WebServCo\Framework\Database;

use WebServCo\Framework\Interfaces\DatabaseInterface;

abstract class AbstractObject
{
    public function __construct(protected DatabaseInterface $db)
    {
    }
}
