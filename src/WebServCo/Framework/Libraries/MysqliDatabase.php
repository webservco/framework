<?php
namespace WebServCo\Framework\Libraries;

final class MysqliDatabase extends \WebServCo\Framework\AbstractLibrary implements
    \WebServCo\Framework\Interfaces\DatabaseInterface
{
    public function __construct($config)
    {
        parent::__construct($config);
    }
    
    public function escape($string)
    {
        throw new \ErrorException('Method not implemented');
    }
}
