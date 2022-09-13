<?php

namespace WebServCo\Framework;

final class Environment
{
    public const DEV = 'dev';
    public const TEST = 'test';
    public const PROD = 'prod';

    /**
     * Retrieve list of all possible environment options.
     *
     * @return array
     */
    public static function getOptions()
    {
        return [self::DEV, self::TEST, self::PROD];
    }
}
