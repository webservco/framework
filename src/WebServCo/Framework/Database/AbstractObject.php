<?php
namespace WebServCo\Framework\Database;

use WebServCo\Framework\Interfaces\DatabaseInterface;

abstract class AbstractObject
{
    protected DatabaseInterface $db;

    public function __construct(DatabaseInterface $db)
    {
        $this->db = $db;
    }
}
