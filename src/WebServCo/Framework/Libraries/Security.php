<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

final class Security extends \WebServCo\Framework\AbstractLibrary
{
    public function getSalt(string $string, int $saltLength = 20): string
    {
        return \strrev(\substr(\sha1(\strtolower($string)), 1, $saltLength));
    }

    public function getHash(string $string, string $salt): string
    {
        return $salt . \sha1($salt . $string);
    }
}
