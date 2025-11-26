<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface RequestInterface
{
    /**
     * Returns data if exists, $defaultValue otherwise.
     */
    public function data(string $key, mixed $defaultValue = null): mixed;

    /**
    * @return array<string,string>
    */
    public function getAcceptContentTypes(): array;

    public function getAcceptLanguage(): string;

    public function getAppUrl(): string;

    /**
    * @return array<int,string>
    */
    public function getArgs(): array;

    public function getBody(): string;

    public function getContentType(): string;

    /**
    * @return array<mixed>
    */
    public function getData(): array;

    public function getHost(): string;

    public function getMethod(): string;

    /**
    * @return array<string,mixed>
    */
    public function getQuery(): array;

    public function getRefererHost(): string;

    public function getRemoteAddress(): string;

    public function getSchema(): string;

    public function getServerVariable(string $index): string;

    public function getSuffix(): string;

    public function getTarget(): string;

    /**
    * @param array<int,string> $removeParameters
    */
    public function getUrl(array $removeParameters = []): string;

    public function getUserAgent(): string;

    /**
     * @param mixed $key Can be an array, a string,
     *                          or a special formatted string
     *                          (eg 'i18n/lang').
     */
    public function query(mixed $key, mixed $defaultValue = null): mixed;
}
