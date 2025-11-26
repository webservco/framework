<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

use WebServCo\Framework\Http\Response;

interface HttpClientInterface
{
    public function reset(): bool;

    public function retrieve(string $url): Response;

    public function setDebug(bool $debug): bool;

    public function setMethod(string $method): bool;

    public function setRequestContentType(string $contentType): bool;

    /**
    * @param array<mixed>|string $data
    */
    public function setRequestData(array|string $data): bool;

    public function setRequestHeader(string $name, string $value): bool;
}
