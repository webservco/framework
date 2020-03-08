<?php
namespace {

    if (!function_exists('__')) {
        /**
         * Gettext / dgetttext wrapper.
         */
        function __($msg, $domain = null)
        {
            if (!empty($domain)) {
                return dgettext($domain, $msg);
            }
            return gettext($msg);
        }
    }

    if (!function_exists('___')) {
        /**
         * Ngettext / dngettext wrapper.
         */
        function ___($msgSingular, $msgPlural, $msgNumber, $domain = null)
        {
            if (!empty($domain)) {
                return dngettext($domain, $msgSingular, $msgPlural, $msgNumber);
            }
            return ngettext($msgSingular, $msgPlural, $msgNumber);
        }
    }
}
