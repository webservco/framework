<?php

declare(strict_types=1);

namespace WebServCo\Framework\Interfaces;

interface I18nInterface
{

    public function getLanguage(): string;

    /**
    * @return array<string, array<string,string>>
    */
    public function getLanguages(): array;

    public function init(string $projectPath, string $lang = ''): bool;

    public function reset(): bool;

    public function setLanguage(string $lang, ?string $translationsPath = null): bool;
}
