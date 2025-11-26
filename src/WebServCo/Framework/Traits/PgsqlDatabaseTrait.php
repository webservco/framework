<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Exceptions\NotImplementedException;

use function sprintf;
use function str_replace;

trait PgsqlDatabaseTrait
{
    public function escapeIdentifier(string $string): string
    {
        return '"' . str_replace('"', '""', $string) . '"';
    }

    public function escapeTableName(string $string): string
    {
        // @TODO Fix.
        throw new NotImplementedException(
            sprintf('Method not implemented. String: %s.', $string),
        );
    }
}
