<?php
namespace WebServCo\Framework;

final class Http
{
    const METHOD_GET = 'GET';
    const METHOD_HEAD = 'HEAD';
    const METHOD_POST = 'POST';
    const METHOD_PUT = 'PUT';
    const METHOD_DELETE = 'DELETE';
    const METHOD_CONNECT = 'CONNECT';
    const METHOD_OPTIONS = 'OPTIONS';
    const METHOD_TRACE = 'TRACE';
    
    /**
     * Retrieve list of supported HTTP methods.
     *
     * @return array
     */
    final public static function getMethods()
    {
        return [self::METHOD_GET, self::METHOD_HEAD, self::METHOD_POST];
    }
}
