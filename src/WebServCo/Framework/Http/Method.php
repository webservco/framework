<?php declare(strict_types = 1);

namespace WebServCo\Framework\Http;

final class Method
{

    public const GET = 'GET';
    public const HEAD = 'HEAD';
    public const POST = 'POST';
    public const PUT = 'PUT';
    public const DELETE = 'DELETE';
    public const CONNECT = 'CONNECT';
    public const OPTIONS = 'OPTIONS';
    public const TRACE = 'TRACE';

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
