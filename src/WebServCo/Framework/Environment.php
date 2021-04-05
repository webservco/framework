<?php

declare(strict_types=1);

namespace WebServCo\Framework;

final class Environment
{
    // Development - local
    public const DEV = 'dev';

    // Development - server
    public const TEST = 'test';

    // Sandbox - server
    public const STAGING = 'staging';

    // Live - server
    public const PRODUCTION = 'production';
}
