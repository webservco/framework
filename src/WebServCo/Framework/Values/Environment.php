<?php

declare(strict_types=1);

namespace WebServCo\Framework\Values;

final class Environment
{
    // Development - local
    public const string DEVELOPMENT = 'development';

    // Test, QC
    public const string TESTING = 'testing';

    // Staging, Model, Pre, Demo
    // Mirror of production environment
    public const string STAGING = 'staging';

    // Production, Live
    public const string PRODUCTION = 'production';
}
