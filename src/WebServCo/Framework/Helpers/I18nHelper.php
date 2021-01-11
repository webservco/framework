<?php
namespace {

    if (!function_exists('__')) {
        /**
         * Gettext / dgetttext wrapper.
         */
        function __(string $msg, string $domain = null) : string
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
        function ___(string $msgSingular, string $msgPlural, int $msgNumber, string $domain = null) : string
        {
            if (!empty($domain)) {
                return dngettext($domain, $msgSingular, $msgPlural, $msgNumber);
            }
            return ngettext($msgSingular, $msgPlural, $msgNumber);
        }
    }
}
