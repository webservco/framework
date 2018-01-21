<?php
namespace WebServCo\Framework\Interfaces;

interface DatabaseInterface
{
    public function escape($string);
    
    public function executeQuery($query, $values = []);
    
    public function executeTransaction($data);
    
    public function numRows();
    
    public function affectedRows();
    
    public function getRows($query, $params = []);
    
    public function getRow($query, $params = []);
    
    public function getColumn($query, $params = [], $columnNumber = 0);
    
    public function lastInsertId();
}
