<?php
namespace WebServCo\Framework\Database\Utils;

final class PreparedStatements
{
    public static function generatePlaceholdersString($data = [])
    {
        return implode(', ', array_map(function () {
            return '?';
        }, $data));
    }
}
