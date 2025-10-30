<?php

declare(strict_types=1);

namespace WebServCo\Framework;

abstract class AbstractUser extends \WebServCo\Framework\AbstractLibrary
{
    public const string ERR_DISABLED = 'ERR_DISABLED';
    public const string ERR_LOGIN = 'ERR_LOGIN';
    public const string ERR_NOT_FOUND = 'ERR_NOT_FOUND';
}
