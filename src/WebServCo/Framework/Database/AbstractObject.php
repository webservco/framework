<?php

namespace WebServCo\Framework\Database;

abstract class AbstractObject
{
    protected $db;

    public function __construct(\WebServCo\Framework\Interfaces\DatabaseInterface $db)
    {
        $this->db = $db;
    }
}
