<?php
namespace WebServCo\Framework\Interfaces;

interface RequestInterface
{
    public function getAcceptContentTypes();
    public function getAppUrl();
    public function getContentType();
    public function getArgs();
    public function getBody();
    public function getMethod();
    public function getQuery();
    public function getRemoteAddress();
    public function getUrl($removeParameters = []);
}
