<?php
namespace WebServCo\Framework\Interfaces;

interface DatabaseInterface
{
    public function escape($string);
    
    public function executeQuery($query, $values = []);
    
    public function executeTransaction($data);
    
    public function numRows();
    
    public function affectedRows();
    
    public function lastInsertId();
}
