<?php

declare(strict_types=1);

namespace WebServCo\Framework\Traits;

trait ExposeLibrariesTrait
{

    final protected function config(): \WebServCo\Framework\Interfaces\ConfigInterface
    {
        return \WebServCo\Framework\Helpers\ConfigLibraryHelper::library();
    }

    final protected function cookie(): \WebServCo\Framework\Libraries\Cookie
    {
        return \WebServCo\Framework\Helpers\CookieLibraryHelper::library();
    }

    final protected function i18n(): \WebServCo\Framework\Interfaces\I18nInterface
    {
        return \WebServCo\Framework\Helpers\I18nLibraryHelper::library();
    }

    final protected function mysqlPdoDb(): \WebServCo\Framework\Interfaces\DatabaseInterface
    {
        return \WebServCo\Framework\Helpers\MysqlPdoDatabaseLibraryHelper::library();
    }

    final protected function request(): \WebServCo\Framework\Interfaces\RequestInterface
    {
        return \WebServCo\Framework\Helpers\RequestLibraryHelper::library();
    }

    final protected function router(): \WebServCo\Framework\Libraries\Router
    {
        return \WebServCo\Framework\Helpers\RouterLibraryHelper::library();
    }

    final protected function security(): \WebServCo\Framework\Libraries\Security
    {
        return \WebServCo\Framework\Helpers\SecurityLibraryHelper::library();
    }

    final protected function session(): \WebServCo\Framework\Interfaces\SessionInterface
    {
        return \WebServCo\Framework\Helpers\SessionLibraryHelper::library();
    }
}
