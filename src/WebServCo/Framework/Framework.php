<?php
namespace WebServCo\Framework;

final class Framework
{
    private static $libraries;
    
    public static function isCLI()
    {
        return 'cli' === PHP_SAPI;
    }
}
