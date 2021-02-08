<?php declare(strict_types = 1);

namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Framework;

trait ExposeLibrariesTrait
{
    final protected function config(): \WebServCo\Framework\Interfaces\ConfigInterface
    {
        return Framework::library('Config');
    }

    final protected function cookie(): \WebServCo\Framework\Libraries\Cookie
    {
        return Framework::library('Cookie');
    }

    final protected function i18n(): \WebServCo\Framework\Interfaces\I18nInterface
    {
        return Framework::library('I18n');
    }

    final protected function mysqlPdoDb(): \WebServCo\Framework\Interfaces\DatabaseInterface
    {
        return Framework::library('MysqlPdoDatabase');
    }

    final protected function request(): \WebServCo\Framework\Interfaces\RequestInterface
    {
        return Framework::library('Request');
    }

    final protected function router(): \WebServCo\Framework\Libraries\Router
    {
        return Framework::library('Router');
    }

    final protected function security(): \WebServCo\Framework\Libraries\Security
    {
        return Framework::library('Security');
    }

    final protected function session(): \WebServCo\Framework\Interfaces\SessionInterface
    {
        return Framework::library('Session');
    }
}
