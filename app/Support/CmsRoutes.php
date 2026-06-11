<?php

namespace App\Support;

use Throwable;

class CmsRoutes
{
    public static function url(?string $routeName, ?string $url = null): string
    {
        if (filled($routeName)) {
            try {
                return route($routeName);
            } catch (Throwable) {
                return $url ?: '#';
            }
        }

        return $url ?: '#';
    }
}
