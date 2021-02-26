<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\I18n;

abstract class I18nLibraryHelper extends AbstractLibraryHelper
{
    private static ?I18n $object = null;

    public static function library(): I18n
    {
        if (!self::$object instanceof I18n) {
            self::loadLibraryHelper('I18n');
            $settings = ConfigLibraryHelper::getSettings('I18n');
            self::$object = new I18n($settings);
        }
        return self::$object;
    }
}
