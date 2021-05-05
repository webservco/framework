<?php

declare(strict_types=1);

namespace WebServCo\Framework\Libraries;

final class I18n extends \WebServCo\Framework\AbstractLibrary implements \WebServCo\Framework\Interfaces\I18nInterface
{

    /**
     * Langs
     *
     * @var array<string, array<string,string>>
     */
    protected array $langs;

    protected string $domain;

    protected string $lang;

    protected string $locale;

    protected string $translationsPath;

    /**
    * @param array<string,string|array<mixed>> $settings
    */
    public function __construct(array $settings = [])
    {
        parent::__construct($settings);

        $this->langs = $this->setting('langs', []);
        $this->domain = $this->setting('domain', 'messages');
    }

    public function getLanguage(): string
    {
        return $this->lang;
    }

    /**
    * @return array<string, array<string,string>>
    */
    public function getLanguages(): array
    {
        return $this->langs;
    }

    public function getLocale(): string
    {
        return $this->locale;
    }

    public function init(string $projectPath, string $lang = ''): bool
    {
        $this->translationsPath = $projectPath . 'resources/translations';

        if (\WebServCo\Framework\Helpers\StringHelper::isEmpty($lang)) {
            $lang = $this->setting('lang', 'en');
        }

        $this->setLanguage($lang);

        return true;
    }

    /**
    * After calling init(), a custom language/domain can be set by calling setLanguage with full arguments.
    * Call this function afterwards to restore the original language/domain.
    */
    public function reset(): bool
    {
        return $this->setLanguage($this->lang, $this->translationsPath);
    }

    public function setLanguage(string $lang, ?string $translationsPath = null): bool
    {
        if (!\array_key_exists($lang, $this->langs)) {
            throw new \WebServCo\Framework\Exceptions\ApplicationException(
                \sprintf('Language not available: %s.', $lang),
            );
        }

        $this->lang = $lang;
        $this->locale = $this->langs[$this->lang]['locale'];

        $this->setLocale($this->locale);
        $this->setDomain($this->domain, $translationsPath ?? $this->translationsPath);

        return true;
    }

    protected function setDomain(string $domain, string $directory): bool
    {
        \bindtextdomain($domain, $directory);
        \textdomain($domain);
        \bind_textdomain_codeset($domain, 'UTF8');

        return true;
    }

    protected function setLocale(string $locale): bool
    {
        /**
         * Rumored to allow using a locale regardless of server locale setup.
         * putenv("LANGUAGE=" . $locale);
         */

        /**
         * Rumored to be needed on Win.
         * putenv("LANG=" . $locale);
         */

        /**
         * Do not use LC_ALL, in order to skip LC_NUMERIC.
         */
        if (\defined('LC_MESSAGES')) {
            \setlocale(\LC_COLLATE, $locale);
            \setlocale(\LC_CTYPE, $locale);
            \setlocale(\LC_MONETARY, $locale);
            \setlocale(\LC_TIME, $locale);
            \setlocale(\LC_MESSAGES, $locale);
        } else { // Windows
            \setlocale(\LC_ALL, $locale);
        }

        return true;
    }
}
