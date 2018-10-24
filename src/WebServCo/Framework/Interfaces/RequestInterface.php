<?php
namespace WebServCo\Framework\Interfaces;

interface RequestInterface
{
    public function getAcceptContentTypes();
    public function getArgs();
    public function getMethod();
    public function getQuery();
    public function getRemoteAddress();
}
