<?php

declare(strict_types=1);

namespace WebServCo\Framework;

final class Environment
{
    const DEV = 'dev';
    const TEST = 'test';
    const PROD = 'prod';

    /**
     * Retrieve list of all possible environment options.
     *
     * @return array<int,string>
     */
    public static function getOptions(): array
    {
        return [self::DEV, self::TEST, self::PROD];
    }
}
