<?php

declare(strict_types=1);

namespace WebServCo\Framework\Helpers;

use WebServCo\Framework\Libraries\Router;

final class RouterLibraryHelper extends AbstractLibraryHelper
{
    private static ?Router $object = null;

    public static function library(): Router
    {
        if (!self::$object instanceof Router) {
            $settings = ConfigLibraryHelper::getSettings('Router');
            self::$object = new Router($settings);
        }
        return self::$object;
    }
}
