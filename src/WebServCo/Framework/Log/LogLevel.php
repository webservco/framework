<?php

declare(strict_types=1);

namespace WebServCo\Framework\Log;

final class LogLevel
{
    public const string EMERGENCY = 'emergency';
    public const string ALERT = 'alert';
    public const string CRITICAL = 'critical';
    public const string ERROR = 'error';
    public const string WARNING = 'warning';
    public const string NOTICE = 'notice';
    public const string INFO = 'info';
    public const string DEBUG = 'debug';

    /**
    * @return array<int,string>
    */
    public static function getList(): array
    {
        return [
            self::EMERGENCY,
            self::ALERT,
            self::CRITICAL,
            self::ERROR,
            self::WARNING,
            self::NOTICE,
            self::INFO,
            self::DEBUG,
        ];
    }
}
