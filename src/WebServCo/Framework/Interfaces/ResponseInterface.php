<?php
namespace WebServCo\Framework\Interfaces;

interface ResponseInterface
{
    public function setStatus($statusCode);

    public function setContent($content);

    public function send();

    public function getContent();

    public function getStatus();
}
