<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

use WebServCo\Framework\AbstractLibrary;

use function sha1;
use function strrev;
use function strtolower;
use function substr;

final class Security extends AbstractLibrary
{
    public function getSalt(string $string, int $saltLength = 20): string
    {
        return strrev(substr(sha1(strtolower($string)), 1, $saltLength));
    }

    public function getHash(string $string, string $salt): string
    {
        return $salt . sha1($salt . $string);
    }
}
