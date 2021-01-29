<?php
namespace WebServCo\Framework\Interfaces;

interface I18nInterface
{
    public function getLanguage(): string;

    /**
    * @return array<string, array<string,string>>
    */
    public function getLanguages(): array;

    public function init(string $projectPath, string $lang = null): bool;

    public function setLanguage(string $lang, string $translationsPath = null): bool;
}
