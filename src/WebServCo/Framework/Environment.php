<?php

namespace WebServCo\Framework;

final class Environment
{
    const DEV = 'dev';
    const TEST = 'test';
    const PROD = 'prod';

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
