<?php
namespace WebServCo\Framework\Database;

abstract class AbstractDatabase extends \WebServCo\Framework\AbstractLibrary
{
    protected $db;
    protected $stmt;
    protected $lastInsertId;
    protected $rows;

    /**
     * Get last inserted Id.
     *
     * https://dev.mysql.com/doc/refman/5.5/en/information-functions.html#function_last-insert-id
     * If you insert multiple rows using a single INSERT statement,
     * LAST_INSERT_ID() returns the value generated for the first inserted row only.
     * The reason for this is to make it possible to reproduce easily the same
     * INSERT statement against some other server.
     */
    public function lastInsertId()
    {
        return (int) $this->lastInsertId;
    }
}
