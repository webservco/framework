<?php
namespace WebServCo\Framework\Traits;

trait MysqlDatabaseTrait
{
    protected function escapeIdentifier($string)
    {
        return '`' . str_replace('`', '``', $string) . '`';
    }
}
