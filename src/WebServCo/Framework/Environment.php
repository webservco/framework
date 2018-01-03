<?php
namespace WebServCo\Framework;

final class Environment
{
    const ENV_DEV = 'dev';
    const ENV_TEST = 'test';
    const ENV_PROD = 'prod';
    
    final public static function getOptions()
    {
        return [self::ENV_DEV, self::ENV_TEST, self::ENV_PROD];
    }
}
