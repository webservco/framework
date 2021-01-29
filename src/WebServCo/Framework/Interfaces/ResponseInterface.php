<?php
namespace WebServCo\Framework\Interfaces;

interface ResponseInterface
{
    public function setStatus(int $statusCode): bool;

    public function setContent(string $content): bool;

    public function send(): int;

    public function getContent(): string;

    public function getStatus(): int;
}
