<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Framework;

trait ExposeLibrariesTrait
{
    final protected function config()
    {
        return Framework::library('Config');
    }

    final protected function date()
    {
        return Framework::library('Date');
    }

    final protected function cookie()
    {
        return Framework::library('Cookie');
    }

    final protected function i18n()
    {
        return Framework::library('I18n');
    }

    final protected function mysqliDb()
    {
        return Framework::library('MysqliDatabase');
    }

    final protected function mysqlPdoDb()
    {
        return Framework::library('MysqlPdoDatabase');
    }

    final protected function request()
    {
        return Framework::library('Request');
    }

    final protected function router()
    {
        return Framework::library('Router');
    }

    final protected function security()
    {
        return Framework::library('Security');
    }

    final protected function session()
    {
        return Framework::library('Session');
    }
}
