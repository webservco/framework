<?php
namespace WebServCo\Framework;

final class Environment
{
    public const ENV_DEV = 'dev';
    public const ENV_TEST = 'test';
    public const ENV_PROD = 'prod';
    
    /**
     * Retrieve list of all possible environment options.
     *
     * @return array
     */
    final public static function getOptions()
    {
        return [self::ENV_DEV, self::ENV_TEST, self::ENV_PROD];
    }
}
