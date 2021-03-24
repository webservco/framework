<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\HtmlOutput;

final class HtmlOutputLibraryHelper extends AbstractLibraryHelper
{
    private static ?HtmlOutput $object = null;

    public static function library(): HtmlOutput
    {
        if (!self::$object instanceof HtmlOutput) {
            $settings = ConfigLibraryHelper::getSettings('HtmlOutput');
            self::$object = new HtmlOutput($settings);
        }
        return self::$object;
    }
}
