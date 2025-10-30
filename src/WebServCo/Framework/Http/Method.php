<?php

declare(strict_types=1);

namespace WebServCo\Framework\Http;

final class Method
{
    public const string GET = 'GET';
    public const string HEAD = 'HEAD';
    public const string POST = 'POST';
    public const string PUT = 'PUT';
    public const string DELETE = 'DELETE';
    public const string CONNECT = 'CONNECT';
    public const string OPTIONS = 'OPTIONS';
    public const string TRACE = 'TRACE';

    /**
     * Retrieve list of supported HTTP methods.
     *
     * @return array <int,string>
     */
    public static function getSupported(): array
    {
        return [
            self::GET,
            self::HEAD,
            self::POST,
            self::PUT,
            self::DELETE,
            self::CONNECT,
            self::OPTIONS,
            self::TRACE,
        ];
    }
}
