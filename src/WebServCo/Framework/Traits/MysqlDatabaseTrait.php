<?php
namespace WebServCo\Framework\Traits;

trait MysqlDatabaseTrait
{
    public function escapeIdentifier($string)
    {
        return '`' . str_replace('`', '``', $string) . '`';
    }
}
