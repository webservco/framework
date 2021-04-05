<?php

declare(strict_types=1);

namespace WebServCo\Framework;

final class Environment
{
    // Development - local
    public const DEVELOPMENT = 'development';

    // Test, QC
    public const TESTING = 'testing';

    // Staging, Model, Pre, Demo
    // Mirror of production environment
    public const STAGING = 'staging';

    // Production, Live
    public const PRODUCTION = 'production';
}
