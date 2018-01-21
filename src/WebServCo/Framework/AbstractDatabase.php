<?php
namespace WebServCo\Framework;

class AbstractDatabase extends \WebServCo\Framework\AbstractLibrary
{
    const QUERY_TYPE_INSERT = 'INSERT';
    const QUERY_TYPE_INSERT_IGNORE = 'INSERT IGNORE';
    const QUERY_TYPE_REPLACE = 'REPLACE';
    
    protected $db;
    protected $stmt;
    protected $lastInsertId;
    protected $rows;
    
    public function __construct($config)
    {
        parent::__construct($config);
    }
    
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
