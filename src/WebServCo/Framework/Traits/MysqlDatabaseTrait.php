<?php
namespace WebServCo\Framework\Traits;

trait MysqlDatabaseTrait
{
    final public function escapeIdentifier($string)
    {
        return '`' . str_replace('`', '``', $string) . '`';
    }
}
