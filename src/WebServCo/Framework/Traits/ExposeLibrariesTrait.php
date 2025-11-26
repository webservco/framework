<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Helpers\ConfigLibraryHelper;
use WebServCo\Framework\Helpers\CookieLibraryHelper;
use WebServCo\Framework\Helpers\I18nLibraryHelper;
use WebServCo\Framework\Helpers\MysqlPdoDatabaseLibraryHelper;
use WebServCo\Framework\Helpers\RequestLibraryHelper;
use WebServCo\Framework\Helpers\RouterLibraryHelper;
use WebServCo\Framework\Helpers\SecurityLibraryHelper;
use WebServCo\Framework\Helpers\SessionLibraryHelper;
use WebServCo\Framework\Interfaces\ConfigInterface;
use WebServCo\Framework\Interfaces\DatabaseInterface;
use WebServCo\Framework\Interfaces\I18nInterface;
use WebServCo\Framework\Interfaces\RequestInterface;
use WebServCo\Framework\Interfaces\SessionInterface;
use WebServCo\Framework\Libraries\Cookie;
use WebServCo\Framework\Libraries\Router;
use WebServCo\Framework\Libraries\Security;

trait ExposeLibrariesTrait
{
    final protected function config(): ConfigInterface
    {
        return ConfigLibraryHelper::library();
    }

    final protected function cookie(): Cookie
    {
        return CookieLibraryHelper::library();
    }

    final protected function i18n(): I18nInterface
    {
        return I18nLibraryHelper::library();
    }

    final protected function mysqlPdoDb(): DatabaseInterface
    {
        return MysqlPdoDatabaseLibraryHelper::library();
    }

    final protected function request(): RequestInterface
    {
        return RequestLibraryHelper::library();
    }

    final protected function router(): Router
    {
        return RouterLibraryHelper::library();
    }

    final protected function security(): Security
    {
        return SecurityLibraryHelper::library();
    }

    final protected function session(): SessionInterface
    {
        return SessionLibraryHelper::library();
    }
}
