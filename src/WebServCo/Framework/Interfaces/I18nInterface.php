<?php

namespace WebServCo\Framework\Interfaces;

interface I18nInterface
{
    public function getLanguage();
    public function init($projectPath, $lang = null);
    public function setLanguage($lang, $translationsPath = null);
}
