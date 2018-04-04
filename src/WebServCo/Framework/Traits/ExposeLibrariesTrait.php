<?php
namespace WebServCo\Framework\Traits;

use WebServCo\Framework\Framework as Fw;

trait ExposeLibrariesTrait
{
    final protected function config()
    {
        return Fw::library('Config');
    }

    final protected function date()
    {
        return Fw::library('Date');
    }

    final protected function cookie()
    {
        return Fw::library('Cookie');
    }

    final protected function i18n()
    {
        return Fw::library('I18n');
    }

    final protected function mysqliDb()
    {
        return Fw::library('MysqliDatabase');
    }

    final protected function mysqlPdoDb()
    {
        return Fw::library('MysqlPdoDatabase');
    }

    final protected function request()
    {
        return Fw::library('Request');
    }

    final protected function router()
    {
        return Fw::library('Router');
    }

    final protected function security()
    {
        return Fw::library('Security');
    }

    final protected function session()
    {
        return Fw::library('Session');
    }
}
