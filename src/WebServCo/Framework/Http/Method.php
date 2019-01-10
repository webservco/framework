<?php
namespace WebServCo\Framework\Http;

final class Method
{
    const GET = 'GET';
    const HEAD = 'HEAD';
    const POST = 'POST';
    const PUT = 'PUT';
    const DELETE = 'DELETE';
    const CONNECT = 'CONNECT';
    const OPTIONS = 'OPTIONS';
    const TRACE = 'TRACE';

    /**
     * Retrieve list of supported HTTP methods.
     *
     * @return array
     */
    public static function getSupported()
    {
        return [
            self::GET,
            self::HEAD,
            self::POST,
            self::PUT,
            self::DELETE,
            self::CONNECT,
            self::OPTIONS,
            self::TRACE
        ];
    }
}
