<?php
namespace WebServCo\Framework\Libraries;

final class I18n extends \WebServCo\Framework\AbstractLibrary
{
    protected $langs;
    protected $domain;
    
    protected $lang;
    
    protected $locale;
    
    protected $translationsPath;
    
    public function __construct($settings = [])
    {
        parent::__construct($settings);
        
        $this->langs = $this->setting('langs', []);
        $this->domain = $this->setting('domain', 'messages');
    }
    
    public function init($projectPath, $lang = null)
    {
        $this->translationsPath = $projectPath . 'resources/translations';
        
        $lang = $lang ? $lang : $this->setting('lang', 'en');
        $this->setLanguage($lang);
        
        return true;
    }
    
    public function setLanguage($lang)
    {
        if (!array_key_exists($lang, $this->langs)) {
            throw new \ErrorException('Language not available');
        }
        
        $this->lang = $lang;
        $this->locale = $this->langs[$this->lang]['locale'];

        $this->setLocale($this->locale);
        $this->setDomain($this->domain, $this->translationsPath);
        
        return true;
    }
    
    public function getLanguage()
    {
        return $this->lang;
    }
    
    public function getLanguages()
    {
        return $this->langs;
    }
    
    protected function setLocale($locale)
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
        setlocale(LC_COLLATE, $locale);
        setlocale(LC_CTYPE, $locale);
        setlocale(LC_MONETARY, $locale);
        setlocale(LC_TIME, $locale);
        setlocale(LC_MESSAGES, $locale);
        
        return true;
    }
    
    protected function setDomain($domain, $directory)
    {
        bindtextdomain($domain, $directory);
        textdomain($domain);
        bind_textdomain_codeset($domain, 'UTF8');
        
        return true;
    }
}
