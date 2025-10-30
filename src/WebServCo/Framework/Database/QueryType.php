<?php

declare(strict_types=1);

namespace WebServCo\Framework\Database;

final class QueryType
{
    public const string INSERT = 'INSERT';
    public const string INSERT_IGNORE = 'INSERT IGNORE';
    public const string REPLACE = 'REPLACE';
}
