<?php
namespace WebServCo\Framework\Interfaces;

interface RequestInterface
{
    public function getArgs();
    public function getMethod();
    public function getQuery();
    public function getRemoteAddress();
}
